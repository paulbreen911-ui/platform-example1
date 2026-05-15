<?php
// viewer.php - Fullscreen 3D Viewer
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STAGE — Viewer</title>
<style>
  *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
  html, body { width: 100%; height: 100%; overflow: hidden; background: #000; }

  #canvas-container {
    position: fixed; inset: 0;
    width: 100vw; height: 100vh;
  }

  #renderer-canvas {
    display: block;
    width: 100% !important;
    height: 100% !important;
  }

  /* HUD overlay */
  #hud {
    position: fixed;
    top: 16px; left: 16px;
    font-family: 'Courier New', monospace;
    font-size: 11px;
    color: rgba(255,255,255,0.4);
    pointer-events: none;
    z-index: 10;
    line-height: 1.6;
  }
  #hud .hud-label { color: rgba(255,140,60,0.6); }

  #status-bar {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    height: 28px;
    background: rgba(0,0,0,0.7);
    border-top: 1px solid rgba(255,140,60,0.2);
    display: flex;
    align-items: center;
    padding: 0 16px;
    gap: 24px;
    font-family: 'Courier New', monospace;
    font-size: 10px;
    color: rgba(255,255,255,0.3);
    pointer-events: none;
    z-index: 10;
  }
  #status-bar .dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #ff8c3c;
    animation: pulse 2s ease-in-out infinite;
  }
  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
  }

  #playhead-bar {
    position: fixed;
    bottom: 28px; left: 0; right: 0;
    height: 3px;
    background: rgba(255,255,255,0.05);
    z-index: 9;
    pointer-events: none;
  }
  #playhead-fill {
    height: 100%;
    background: linear-gradient(90deg, #ff8c3c, #ffcc44);
    width: 0%;
    transition: width 0.05s linear;
  }

  #no-scene {
    position: fixed; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: rgba(255,255,255,0.15);
    font-family: 'Courier New', monospace;
    pointer-events: none;
    z-index: 5;
  }
  #no-scene .big { font-size: 72px; opacity: 0.3; }
  #no-scene .msg { font-size: 13px; margin-top: 16px; letter-spacing: 0.2em; text-transform: uppercase; }
  #no-scene .sub { font-size: 10px; margin-top: 8px; opacity: 0.5; }
</style>
</head>
<body>

<div id="canvas-container">
  <canvas id="renderer-canvas"></canvas>
</div>

<div id="hud">
  <div><span class="hud-label">CAM </span><span id="hud-cam">0.00, 0.00, 5.00</span></div>
  <div><span class="hud-label">ROT </span><span id="hud-rot">0.00°, 0.00°</span></div>
  <div><span class="hud-label">FPS </span><span id="hud-fps">60</span></div>
  <div><span class="hud-label">TIME</span><span id="hud-time">0.000s</span></div>
  <div><span class="hud-label">OBJ </span><span id="hud-obj">0</span></div>
</div>

<div id="playhead-bar"><div id="playhead-fill"></div></div>

<div id="status-bar">
  <div class="dot"></div>
  <span>STAGE VIEWER</span>
  <span id="status-conn">WAITING FOR CONTROLLER...</span>
  <span id="status-mode">IDLE</span>
</div>

<div id="no-scene">
  <div class="big">⬡</div>
  <div class="msg">Stage Viewer Ready</div>
  <div class="sub">Open controller.php to load scene objects</div>
</div>

<!-- Three.js r128 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<script>
// ─── SCENE SETUP ──────────────────────────────────────────────────────────────
const canvas = document.getElementById('renderer-canvas');
const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
renderer.setPixelRatio(window.devicePixelRatio);
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.0;

const scene = new THREE.Scene();
scene.background = new THREE.Color(0x0a0a0a);
scene.fog = null;

const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.01, 10000);
camera.position.set(0, 0, 5);

// Resize handler
function onResize() {
  camera.aspect = window.innerWidth / window.innerHeight;
  camera.updateProjectionMatrix();
  renderer.setSize(window.innerWidth, window.innerHeight);
}
window.addEventListener('resize', onResize);
onResize();

// ─── LIGHTS ───────────────────────────────────────────────────────────────────
const ambientLight = new THREE.AmbientLight(0xffffff, 0.3);
scene.add(ambientLight);

const lightMap = {}; // id → light object

// ─── OBJECTS ──────────────────────────────────────────────────────────────────
const objectMap = {}; // id → mesh
const noScene = document.getElementById('no-scene');

// ─── KEYFRAME / TIMELINE ──────────────────────────────────────────────────────
let timeline = {
  duration: 10,
  currentTime: 0,
  playing: false,
  loop: false,
  keyframes: {} // objectId → [ {time, position, rotation, scale} ]
};
let lastRealTime = null;

function lerpKeyframes(frames, t) {
  if (!frames || frames.length === 0) return null;
  if (frames.length === 1) return frames[0];
  // Find surrounding keyframes
  let before = frames[0], after = frames[frames.length - 1];
  for (let i = 0; i < frames.length - 1; i++) {
    if (frames[i].time <= t && frames[i+1].time >= t) {
      before = frames[i]; after = frames[i+1]; break;
    }
  }
  if (before === after) return before;
  const alpha = (t - before.time) / (after.time - before.time);
  return {
    position: {
      x: before.position.x + (after.position.x - before.position.x) * alpha,
      y: before.position.y + (after.position.y - before.position.y) * alpha,
      z: before.position.z + (after.position.z - before.position.z) * alpha,
    },
    rotation: {
      x: before.rotation.x + (after.rotation.x - before.rotation.x) * alpha,
      y: before.rotation.y + (after.rotation.y - before.rotation.y) * alpha,
      z: before.rotation.z + (after.rotation.z - before.rotation.z) * alpha,
    },
    scale: {
      x: before.scale.x + (after.scale.x - before.scale.x) * alpha,
      y: before.scale.y + (after.scale.y - before.scale.y) * alpha,
      z: before.scale.z + (after.scale.z - before.scale.z) * alpha,
    },
  };
}

function applyTimelineAtTime(t) {
  for (const [id, frames] of Object.entries(timeline.keyframes)) {
    const obj = objectMap[id];
    if (!obj) continue;
    const state = lerpKeyframes(frames, t);
    if (!state) continue;
    obj.position.set(state.position.x, state.position.y, state.position.z);
    obj.rotation.set(
      THREE.MathUtils.degToRad(state.rotation.x),
      THREE.MathUtils.degToRad(state.rotation.y),
      THREE.MathUtils.degToRad(state.rotation.z)
    );
    obj.scale.set(state.scale.x, state.scale.y, state.scale.z);
  }
}

// ─── OBJ LOADER (inline minimal) ─────────────────────────────────────────────
function parseOBJ(text) {
  const positions = [], normals = [], uvs = [];
  const vertices = [], normalsOut = [], uvsOut = [];
  const lines = text.split('\n');
  for (const rawLine of lines) {
    const line = rawLine.trim();
    if (line.startsWith('v ')) {
      const p = line.split(/\s+/);
      positions.push(parseFloat(p[1]), parseFloat(p[2]), parseFloat(p[3]));
    } else if (line.startsWith('vn ')) {
      const p = line.split(/\s+/);
      normals.push(parseFloat(p[1]), parseFloat(p[2]), parseFloat(p[3]));
    } else if (line.startsWith('vt ')) {
      const p = line.split(/\s+/);
      uvs.push(parseFloat(p[1]), parseFloat(p[2]));
    } else if (line.startsWith('f ')) {
      const parts = line.split(/\s+/).slice(1);
      // triangulate fan
      const parsed = parts.map(p => {
        const s = p.split('/');
        return {
          vi: parseInt(s[0]) - 1,
          ti: s[1] ? parseInt(s[1]) - 1 : -1,
          ni: s[2] ? parseInt(s[2]) - 1 : -1,
        };
      });
      for (let i = 1; i < parsed.length - 1; i++) {
        [parsed[0], parsed[i], parsed[i+1]].forEach(({ vi, ti, ni }) => {
          vertices.push(positions[vi*3], positions[vi*3+1], positions[vi*3+2]);
          if (ni >= 0 && normals.length) normalsOut.push(normals[ni*3], normals[ni*3+1], normals[ni*3+2]);
          if (ti >= 0 && uvs.length) uvsOut.push(uvs[ti*2], uvs[ti*2+1]);
        });
      }
    }
  }
  const geo = new THREE.BufferGeometry();
  geo.setAttribute('position', new THREE.Float32BufferAttribute(vertices, 3));
  if (normalsOut.length === vertices.length) geo.setAttribute('normal', new THREE.Float32BufferAttribute(normalsOut, 3));
  else geo.computeVertexNormals();
  if (uvsOut.length) geo.setAttribute('uv', new THREE.Float32BufferAttribute(uvsOut, 2));
  return geo;
}

// ─── BROADCAST CHANNEL ────────────────────────────────────────────────────────
const bc = new BroadcastChannel('stage_channel');
const statusConn = document.getElementById('status-conn');
const statusMode = document.getElementById('status-mode');

bc.onmessage = (evt) => {
  const msg = evt.data;
  handleMessage(msg);
};

function handleMessage(msg) {
  switch (msg.type) {

    case 'ping':
      statusConn.textContent = 'CONTROLLER CONNECTED';
      bc.postMessage({ type: 'pong' });
      break;

    case 'camera':
      camera.position.set(msg.x, msg.y, msg.z);
      camera.rotation.set(
        THREE.MathUtils.degToRad(msg.rx),
        THREE.MathUtils.degToRad(msg.ry),
        0, 'YXZ'
      );
      camera.fov = msg.fov || 60;
      camera.updateProjectionMatrix();
      break;

    case 'background':
      if (msg.color) {
        scene.background = new THREE.Color(msg.color);
        scene.fog = null;
      }
      if (msg.fog) {
        scene.fog = new THREE.FogExp2(new THREE.Color(msg.fogColor || '#000000'), msg.fogDensity || 0.02);
      }
      renderer.toneMappingExposure = msg.exposure || 1.0;
      break;

    case 'add_object': {
      noScene.style.display = 'none';
      const geo = parseOBJ(msg.objData);
      const mat = new THREE.MeshStandardMaterial({
        color: 0xcccccc,
        roughness: 0.5,
        metalness: 0.1,
      });
      const mesh = new THREE.Mesh(geo, mat);
      mesh.castShadow = true;
      mesh.receiveShadow = true;
      // Auto-normalize
      geo.computeBoundingBox();
      const box = geo.boundingBox;
      const size = new THREE.Vector3();
      box.getSize(size);
      const maxDim = Math.max(size.x, size.y, size.z);
      if (maxDim > 0) mesh.scale.setScalar(2 / maxDim);
      geo.computeBoundingSphere();
      const center = geo.boundingSphere.center;
      mesh.position.set(-center.x * mesh.scale.x, -center.y * mesh.scale.y, -center.z * mesh.scale.z);
      scene.add(mesh);
      objectMap[msg.id] = mesh;
      updateHUDObj();
      break;
    }

    case 'apply_texture': {
      const obj = objectMap[msg.id];
      if (!obj) break;
      const img = new Image();
      img.onload = () => {
        const tex = new THREE.Texture(img);
        tex.needsUpdate = true;
        obj.material.map = tex;
        obj.material.needsUpdate = true;
      };
      img.src = msg.dataUrl;
      break;
    }

    case 'object_transform': {
      const obj = objectMap[msg.id];
      if (!obj) break;
      if (msg.position) obj.position.set(msg.position.x, msg.position.y, msg.position.z);
      if (msg.rotation) obj.rotation.set(
        THREE.MathUtils.degToRad(msg.rotation.x),
        THREE.MathUtils.degToRad(msg.rotation.y),
        THREE.MathUtils.degToRad(msg.rotation.z)
      );
      if (msg.scale !== undefined) obj.scale.setScalar(msg.scale);
      if (msg.color) obj.material.color.set(msg.color);
      if (msg.roughness !== undefined) obj.material.roughness = msg.roughness;
      if (msg.metalness !== undefined) obj.material.metalness = msg.metalness;
      if (msg.wireframe !== undefined) obj.material.wireframe = msg.wireframe;
      obj.material.needsUpdate = true;
      break;
    }

    case 'remove_object': {
      const obj = objectMap[msg.id];
      if (obj) { scene.remove(obj); obj.geometry.dispose(); obj.material.dispose(); delete objectMap[msg.id]; }
      updateHUDObj();
      break;
    }

    case 'light': {
      // Remove old if exists
      if (lightMap[msg.id]) { scene.remove(lightMap[msg.id]); delete lightMap[msg.id]; }
      if (msg.action === 'remove') break;
      let light;
      const col = new THREE.Color(msg.color || '#ffffff');
      const intensity = msg.intensity || 1;
      switch (msg.lightType) {
        case 'ambient':    light = new THREE.AmbientLight(col, intensity); break;
        case 'point':      light = new THREE.PointLight(col, intensity, msg.distance || 100); light.castShadow = true; break;
        case 'spot':       light = new THREE.SpotLight(col, intensity); light.castShadow = true; light.angle = (msg.angle || 45) * Math.PI / 180; break;
        case 'directional': light = new THREE.DirectionalLight(col, intensity); light.castShadow = true; break;
        case 'hemisphere': light = new THREE.HemisphereLight(col, new THREE.Color(msg.groundColor || '#333333'), intensity); break;
        default:           light = new THREE.PointLight(col, intensity);
      }
      if (msg.position && light.position) light.position.set(msg.position.x, msg.position.y, msg.position.z);
      scene.add(light);
      lightMap[msg.id] = light;
      break;
    }

    case 'timeline_update':
      timeline.duration = msg.duration || timeline.duration;
      timeline.loop = msg.loop || false;
      if (msg.keyframes) timeline.keyframes = msg.keyframes;
      break;

    case 'transport_play':
      timeline.playing = true;
      lastRealTime = null;
      statusMode.textContent = '▶ PLAYING';
      break;

    case 'transport_pause':
      timeline.playing = false;
      statusMode.textContent = '⏸ PAUSED';
      break;

    case 'transport_stop':
      timeline.playing = false;
      timeline.currentTime = 0;
      applyTimelineAtTime(0);
      statusMode.textContent = '⏹ STOPPED';
      document.getElementById('playhead-fill').style.width = '0%';
      break;

    case 'transport_seek':
      timeline.currentTime = msg.time;
      applyTimelineAtTime(msg.time);
      document.getElementById('playhead-fill').style.width = ((msg.time / timeline.duration) * 100).toFixed(2) + '%';
      break;

    case 'scene_clear':
      for (const [id, obj] of Object.entries(objectMap)) {
        scene.remove(obj); obj.geometry.dispose(); obj.material.dispose();
      }
      for (const [id, l] of Object.entries(lightMap)) scene.remove(l);
      Object.keys(objectMap).forEach(k => delete objectMap[k]);
      Object.keys(lightMap).forEach(k => delete lightMap[k]);
      timeline.keyframes = {};
      noScene.style.display = 'flex';
      updateHUDObj();
      break;
  }
}

// ─── HUD UPDATE ───────────────────────────────────────────────────────────────
function updateHUDObj() {
  document.getElementById('hud-obj').textContent = Object.keys(objectMap).length;
}

// ─── RENDER LOOP ──────────────────────────────────────────────────────────────
let frameCount = 0, lastFpsTime = performance.now();

function animate(now) {
  requestAnimationFrame(animate);

  // FPS
  frameCount++;
  if (now - lastFpsTime >= 1000) {
    document.getElementById('hud-fps').textContent = frameCount;
    frameCount = 0; lastFpsTime = now;
  }

  // Timeline playback
  if (timeline.playing) {
    if (lastRealTime === null) lastRealTime = now;
    const delta = (now - lastRealTime) / 1000;
    lastRealTime = now;
    timeline.currentTime += delta;
    if (timeline.currentTime >= timeline.duration) {
      if (timeline.loop) timeline.currentTime %= timeline.duration;
      else { timeline.currentTime = timeline.duration; timeline.playing = false; statusMode.textContent = '⏹ STOPPED'; }
    }
    applyTimelineAtTime(timeline.currentTime);
    document.getElementById('playhead-fill').style.width = ((timeline.currentTime / timeline.duration) * 100).toFixed(2) + '%';
    bc.postMessage({ type: 'time_update', time: timeline.currentTime });
  } else {
    lastRealTime = null;
  }

  // HUD
  const p = camera.position;
  document.getElementById('hud-cam').textContent = `${p.x.toFixed(2)}, ${p.y.toFixed(2)}, ${p.z.toFixed(2)}`;
  const ry = THREE.MathUtils.radToDeg(camera.rotation.y).toFixed(1);
  const rx = THREE.MathUtils.radToDeg(camera.rotation.x).toFixed(1);
  document.getElementById('hud-rot').textContent = `${rx}°, ${ry}°`;
  document.getElementById('hud-time').textContent = timeline.currentTime.toFixed(3) + 's';

  renderer.render(scene, camera);
}
animate(performance.now());

// Ping controller on load
setInterval(() => bc.postMessage({ type: 'viewer_ready' }), 3000);
bc.postMessage({ type: 'viewer_ready' });
</script>
</body>
</html>
