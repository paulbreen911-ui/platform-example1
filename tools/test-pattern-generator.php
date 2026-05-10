<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
$page_title = 'Test Pattern Generator';
$_username  = $_SESSION['username'] ?? null;
$_user_id   = $_SESSION['user_id']  ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test Pattern Generator — Production Central</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&family=Share+Tech+Mono&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
<style>
/* ── Tool colour tokens — matched to Production Central homepage palette ── */

  :root {
    --bg: #0E0E0C;
    --panel: #1A1A17;
    --panel2: #242420;
    --border: rgba(255,255,255,0.07);
    --accent: #C9A84C;
    --accent2: #C9A84C;
    --accent3: #4caf50;
    --warn: #ffb300;
    --danger: #ff4444;
    --text: #E8E6DC;
    --text2: rgba(232,230,220,0.55);
    --text3: rgba(232,230,220,0.28);
    --mono: 'Share Tech Mono', monospace;
    --sans: 'Barlow', sans-serif;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--sans);
    min-height: 100vh;
    padding: 24px 20px 40px;
  }

  /* Subtle grid background */
  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image:
      linear-gradient(rgba(201,168,76,0.025) 1px, transparent 1px),
      linear-gradient(90deg, rgba(201,168,76,0.025) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
    z-index: 0;
  }

  .wrap { position: relative; z-index: 1; max-width: 960px; margin: 0 auto; }

  /* Header */
  header { margin-bottom: 32px; }
  .header-tag {
    font-family: var(--mono);
    font-size: 11px;
    color: var(--accent);
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 8px;
    opacity: 0.8;
  }
  h1 {
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.5px;
    color: var(--text);
    line-height: 1.1;
  }
  h1 span { color: var(--accent); }
  .subtitle {
    margin-top: 6px;
    font-size: 14px;
    color: var(--text2);
    font-weight: 300;
  }

  /* Sections */
  .section {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 20px 24px;
    margin-bottom: 16px;
  }
  .section-title {
    font-family: var(--mono);
    font-size: 11px;
    color: var(--accent);
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .section-title::before {
    content: '';
    display: inline-block;
    width: 6px; height: 6px;
    background: var(--accent);
    border-radius: 50%;
    flex-shrink: 0;
  }

  /* Grid layouts */
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
  .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }

  @media (max-width: 640px) {
    .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr 1fr; }
  }

  /* Form controls */
  .field { display: flex; flex-direction: column; gap: 6px; }
  label {
    font-size: 11px;
    color: var(--text2);
    letter-spacing: 1px;
    text-transform: uppercase;
    font-weight: 500;
  }
  select, input[type="number"] {
    background: var(--panel2);
    border: 1px solid var(--border);
    border-radius: 4px;
    color: var(--text);
    font-family: var(--mono);
    font-size: 13px;
    padding: 8px 10px;
    outline: none;
    transition: border-color 0.15s;
    width: 100%;
    appearance: none;
    -webkit-appearance: none;
  }
  select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%237a9ab5'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    padding-right: 28px;
    cursor: pointer;
  }
  select:focus, input:focus { border-color: var(--accent); }
  select option { background: #242420; }

  /* Number spinner */
  .spinner-wrap { display: flex; align-items: center; gap: 0; }
  .spinner-wrap input {
    border-radius: 4px 0 0 4px;
    border-right: none;
    text-align: center;
  }
  .spin-btn {
    background: var(--panel2);
    border: 1px solid var(--border);
    color: var(--text2);
    font-size: 16px;
    width: 32px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    height: 36px;
    transition: all 0.1s;
    user-select: none;
    flex-shrink: 0;
  }
  .spin-btn:first-of-type { border-radius: 0; }
  .spin-btn:last-of-type { border-radius: 0 4px 4px 0; }
  .spin-btn:hover { background: var(--border); color: var(--accent); }
  .spin-btn:active { transform: scale(0.95); }

  /* Results */
  .results-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
  @media (max-width: 640px) { .results-grid { grid-template-columns: 1fr 1fr; } }

  .result-card {
    background: var(--panel2);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 14px 16px;
    position: relative;
    overflow: visible;
  }
  .result-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: var(--accent);
  }
  .result-card.warn::before { background: var(--warn); }
  .result-card.danger::before { background: var(--danger); }
  .result-card.ok::before { background: var(--accent3); }

  .result-label {
    font-size: 10px;
    color: var(--text2);
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 6px;
  }
  .result-value {
    font-family: var(--mono);
    font-size: 22px;
    color: var(--text);
    line-height: 1;
  }
  .result-value.warn { color: var(--warn); }
  .result-value.danger { color: var(--danger); }
  .result-value.ok { color: var(--accent3); }
  .result-unit {
    font-family: var(--mono);
    font-size: 11px;
    color: var(--text3);
    margin-top: 3px;
  }

  /* Link analysis */
  .link-analysis { margin-top: 16px; }
  .link-row {
    display: grid;
    grid-template-columns: 180px 1fr 90px 90px;
    gap: 10px;
    align-items: center;
    padding: 10px 12px;
    border-radius: 4px;
    margin-bottom: 6px;
    background: var(--panel2);
    border: 1px solid var(--border);
    font-size: 13px;
  }
  .link-row.header {
    background: transparent;
    border-color: transparent;
    font-size: 10px;
    color: var(--text3);
    letter-spacing: 1px;
    text-transform: uppercase;
    padding-bottom: 4px;
  }
  .link-name { font-family: var(--mono); font-size: 12px; color: var(--text2); }
  .link-bar-wrap { position: relative; height: 6px; background: var(--border); border-radius: 3px; overflow: hidden; }
  .link-bar { height: 100%; border-radius: 3px; transition: width 0.3s ease; }
  .link-util { font-family: var(--mono); font-size: 12px; text-align: right; }
  .link-status { font-size: 11px; text-align: center; padding: 2px 6px; border-radius: 3px; font-weight: 600; }
  .status-ok { background: rgba(76,175,80,0.15); color: var(--accent3); }
  .status-warn { background: rgba(255,179,0,0.15); color: var(--warn); }
  .status-crit { background: rgba(255,68,68,0.15); color: var(--danger); }

  /* Info box */
  .info-box {
    background: rgba(0,212,255,0.04);
    border: 1px solid rgba(0,212,255,0.15);
    border-radius: 6px;
    padding: 12px 14px;
    font-size: 12px;
    color: var(--text2);
    line-height: 1.6;
    margin-top: 12px;
  }
  .info-box strong { color: var(--accent); font-weight: 600; }

  /* Divider */
  .divider { border: none; border-top: 1px solid var(--border); margin: 16px 0; }

  /* Formula display */
  .formula {
    font-family: var(--mono);
    font-size: 11px;
    color: var(--text3);
    background: var(--bg);
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid var(--border);
    margin-top: 10px;
    line-height: 1.8;
  }
  .formula span { color: var(--accent2); }

  /* Logo bar */
  .logo-bar {
    width: 100%;
    padding: 0 0 20px;
    margin-bottom: 8px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .logo-bar img {
    width: 25%;
    height: auto;
    display: block;
  }
  @media (max-width: 640px) {
    .logo-bar img { width: 50%; }
  }

  /* Footer */
  footer {
    margin-top: 24px;
    font-size: 11px;
    color: var(--text3);
    text-align: center;
    font-family: var(--mono);
    line-height: 1.8;
  }
  .footer-main {
    font-size: 12px;
    color: var(--text2);
    margin-bottom: 2px;
  }
  .footer-meta {
    font-size: 10px;
    letter-spacing: 1px;
    margin-bottom: 6px;
  }
  .footer-disclaimer {
    font-size: 10px;
    color: var(--text3);
    max-width: 580px;
    margin: 0 auto;
    line-height: 1.6;
    border-top: 1px solid var(--border);
    padding-top: 8px;
  }

  /* Tabs */
  .tab-nav {
    display: flex;
    gap: 4px;
    margin-bottom: 20px;
    border-bottom: 1px solid var(--border);
    padding-bottom: 0;
  }
  .tab-btn {
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    color: var(--text2);
    font-family: var(--sans);
    font-size: 13px;
    font-weight: 500;
    padding: 8px 16px 10px;
    cursor: pointer;
    transition: all 0.15s;
    margin-bottom: -1px;
    letter-spacing: 0.3px;
  }
  .tab-btn:hover { color: var(--text); }
  .tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); }
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }

  /* Transfer rows */
  .xfer-row {
    display: grid;
    grid-template-columns: 220px 1fr 110px 110px;
    gap: 10px;
    align-items: center;
    padding: 9px 12px;
    border-radius: 4px;
    margin-bottom: 5px;
    background: var(--panel2);
    border: 1px solid var(--border);
    font-size: 13px;
  }
  .xfer-row.header {
    background: transparent;
    border-color: transparent;
    font-size: 10px;
    color: var(--text3);
    letter-spacing: 1px;
    text-transform: uppercase;
    padding-bottom: 2px;
  }
  .xfer-name { font-family: var(--mono); font-size: 12px; color: var(--text2); }
  .xfer-bar-wrap { position: relative; height: 6px; background: var(--border); border-radius: 3px; overflow: hidden; }
  .xfer-bar { height: 100%; border-radius: 3px; transition: width 0.3s ease; min-width: 2px; }
  .xfer-time { font-family: var(--mono); font-size: 12px; text-align: right; color: var(--text); }
  .xfer-speed { font-family: var(--mono); font-size: 11px; color: var(--text3); text-align: right; }


  /* Timecode layout */
  .tc-row {
    display: flex;
    align-items: flex-end;
    gap: 0;
    flex-wrap: wrap;
  }
  .tc-field { display: flex; flex-direction: column; gap: 5px; }
  .tc-label {
    font-family: var(--mono);
    font-size: 10px;
    color: var(--text3);
    letter-spacing: 2px;
    text-align: center;
  }
  .tc-sep {
    font-family: var(--mono);
    font-size: 20px;
    color: var(--accent);
    padding: 0 4px;
    padding-bottom: 6px;
    align-self: flex-end;
  }
  .tc-frame-sep { color: var(--text3); }
  .tc-divider {
    width: 1px;
    height: 36px;
    background: var(--border);
    margin: 0 16px;
    align-self: flex-end;
  }
  .tc-clips {}

  /* Tooltip */
  .tooltip-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 14px; height: 14px;
    background: var(--border);
    color: var(--text2);
    border-radius: 50%;
    font-size: 9px;
    cursor: pointer;
    position: relative;
    margin-left: 4px;
    vertical-align: middle;
    font-weight: bold;
    font-style: normal;
  }
  .tooltip-box {
    display: none;
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    background: #242420;
    border: 1px solid var(--accent);
    border-radius: 5px;
    padding: 10px 12px;
    width: 220px;
    font-size: 11.5px;
    color: #e0e8f0;
    line-height: 1.6;
    z-index: 9999;
    font-weight: normal;
    letter-spacing: 0;
    text-transform: none;
    white-space: normal;
    pointer-events: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  }
  .tooltip-wrap:hover .tooltip-box { display: block; }


  /* Timecode container */
  .tc-outer-row {
    display: flex;
    align-items: flex-end;
    gap: 24px;
    flex-wrap: wrap;
  }
  .tc-container {
    display: flex;
    align-items: center;
    background: var(--panel2);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 10px 16px;
    gap: 2px;
  }
  .tc-unit {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
  }
  .tc-input {
    background: transparent;
    border: none;
    color: var(--text);
    font-family: var(--mono);
    font-size: 22px;
    font-weight: bold;
    width: 48px;
    text-align: center;
    outline: none;
    padding: 2px 0;
    -moz-appearance: textfield;
  }
  .tc-input::-webkit-inner-spin-button,
  .tc-input::-webkit-outer-spin-button { -webkit-appearance: none; }
  .tc-input:focus { color: var(--accent); }
  .tc-clips-wrap { padding-bottom: 2px; }

  /* Speed mode toggle */
  .speed-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .speed-label {
    font-family: var(--mono);
    font-size: 10px;
    color: var(--text3);
    letter-spacing: 1px;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
  }
  .toggle-btns {
    display: flex;
    background: var(--panel2);
    border: 1px solid var(--border);
    border-radius: 4px;
    overflow: visible;
  }
  .toggle-btn {
    background: none;
    border: none;
    color: var(--text2);
    font-family: var(--sans);
    font-size: 11px;
    font-weight: 500;
    padding: 5px 12px;
    cursor: pointer;
    transition: all 0.15s;
  }
  .toggle-btn:hover { color: var(--text); }
  .toggle-btn.active { background: var(--accent); color: #000; font-weight: 600; }



  /* PDF export button */
  .pdf-btn {
    background: none;
    border: 1px solid var(--accent);
    color: var(--accent);
    font-family: var(--mono);
    font-size: 11px;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 7px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s;
    display: flex;
    align-items: center;
    gap: 7px;
  }
  .pdf-btn:hover { background: var(--accent); color: #000; }
  .pdf-btn svg { width: 13px; height: 13px; flex-shrink: 0; }


  /* ── PROJECTION TAB ──────────────────────────────────────────────── */
  .proj-diagram {
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 16px;
    margin-top: 12px;
    font-family: var(--mono);
    font-size: 11px;
    color: var(--text3);
    text-align: center;
    line-height: 2;
  }
  .proj-diagram .diag-value { color: var(--accent); font-size: 13px; }
  .proj-diagram .diag-label { color: var(--text2); }
  .blend-visual {
    display: flex;
    align-items: stretch;
    gap: 0;
    margin-top: 12px;
    border-radius: 4px;
    overflow: hidden;
    font-family: var(--mono);
    font-size: 10px;
    /* height set dynamically from aspect ratio */
  }
  .stp-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 7px 12px; border-bottom: 1px solid var(--border);
    gap: 8px;
  }
  .stp-row:last-child { border-bottom: none; }
  .stp-lbl {
    font-family: var(--mono); font-size: 11px; color: var(--text2);
    white-space: nowrap; flex-shrink: 0;
  }
  .stp-unit {
    font-family: var(--mono); font-size: 10px; color: var(--text3);
  }
  .stp-row-divider {
    height: 1px; background: var(--border); margin: 4px 0;
  }
  .stp-picker-wrap {
    position: relative; display: inline-block;
  }
  .stp-picker-panel {
    display: none; position: absolute; z-index: 100; top: 36px; left: 0;
    background: var(--panel); border: 1px solid var(--border);
    border-radius: 8px; padding: 14px; min-width: 220px; box-shadow: 0 4px 20px rgba(0,0,0,0.5);
  }
  .stp-picker-panel.open { display: block; }
  .stp-picker-preview {
    width: 100%; height: 32px; border-radius: 4px; margin-bottom: 10px;
    border: 1px solid var(--border);
  }
  .stp-picker-row {
    display: flex; align-items: center; gap: 8px; margin-bottom: 8px;
  }
  .stp-picker-label {
    font-family: var(--mono); font-size: 10px; color: var(--text2); width: 16px; flex-shrink: 0;
  }
  .stp-picker-row input[type=range] {
    flex: 1; height: 3px; accent-color: var(--accent);
  }
  .stp-picker-val {
    font-family: var(--mono); font-size: 11px; color: var(--accent); width: 30px; text-align: right;
  }
  .stp-picker-hex {
    width: 100%; background: var(--panel2); border: 1px solid var(--border);
    border-radius: 4px; color: var(--text); font-family: var(--mono); font-size: 12px;
    padding: 6px 8px; outline: none; margin-bottom: 8px; text-transform: uppercase;
  }
  .stp-adj-card {
    background: var(--panel2); border: 1px solid var(--border);
    border-radius: 6px; padding: 8px 12px;
    display: flex; flex-direction: column; gap: 7px;
  }
  .stp-adj-label {
    font-family: var(--mono); font-size: 9px; letter-spacing: 1px;
    text-transform: uppercase; color: var(--text2);
  }
  .stp-pill {
    display: inline-flex; align-items: center; padding: 3px 10px;
    border: 1px solid var(--border); border-radius: 20px;
    font-family: var(--mono); font-size: 10px; color: var(--text3);
    cursor: pointer; user-select: none; white-space: nowrap;
    transition: all 0.1s; letter-spacing: 0.3px;
  }
  .stp-pill-on {
    border-color: var(--accent); color: var(--accent);
    background: rgba(0,212,255,0.07);
  }
  .blend-seg {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000;
    font-weight: 600;
    letter-spacing: 0.5px;
  }
  .blend-seg.img   { background: var(--accent3); opacity: 0.85; }
  .blend-seg.blend { background: var(--accent);  opacity: 0.9; }
  .proj-result-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 12px;
  }
  @media (max-width: 640px) { .proj-result-grid { grid-template-columns: 1fr 1fr; } }
  .ambient-btns {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }
  .amb-btn {
    background: var(--panel2);
    border: 1px solid var(--border);
    color: var(--text2);
    font-family: var(--mono);
    font-size: 11px;
    padding: 5px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s;
    letter-spacing: 0.5px;
  }
  .amb-btn:hover { border-color: var(--accent); color: var(--accent); }
  .amb-btn.active { background: var(--accent); color: #000; border-color: var(--accent); font-weight: 600; }

  /* ── PDF / PRINT STYLES ─────────────────────────────────────────── */
  @page {
    size: A4 landscape;
    margin-top: 18mm;
    margin-bottom: 12mm;
    margin-left: 8mm;
    margin-right: 8mm;
  }
  @media print {
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    html {
      background: #0a0e14 !important;
    }
    /* Tab 1 (ST 2110) — less content, higher zoom */
    html.print-tab-st2110 { zoom: 0.75; }
    /* Tab 2 (Media) — more content, lower zoom to fit one page */
    html.print-tab-media      { zoom: 0.58; }
    html.print-tab-projection { zoom: 0.52; }
    body {
      background: #0a0e14 !important;
      color: #e0e8f0 !important;
      padding: 12px 16px !important;
      margin: 0 !important;
      max-width: none !important;
      width: 100% !important;
    }
    /* Remove the max-width constraint on the wrap so it fills the page */
    .wrap {
      max-width: none !important;
      width: 100% !important;
    }
    body::before { display: none; }
    .tab-nav, footer { display: none !important; }
    .tab-panel { display: none !important; }
    .tab-panel.active { display: block !important; }
    .section { break-inside: avoid; margin-bottom: 10px; }
    header { margin-bottom: 12px; }
    .logo-bar { padding-bottom: 10px; margin-bottom: 4px; }
    .logo-bar img { width: 15% !important; }
    .pdf-btn { display: none !important; }
    .xfer-row { break-inside: avoid; }
    .link-bar, .xfer-bar { -webkit-print-color-adjust: exact !important; }
  }

  input[type="number"] { text-align: center; }
  input[type="number"]::-webkit-inner-spin-button,
  input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
  input[type="number"] { -moz-appearance: textfield; }


/* ── PC nav bar — matches homepage nav exactly ── */
:root {
  --black: #0E0E0C;
  --dark:  #1A1A17;
  --dark-2:#242420;
  --gold:  #C9A84C;
  --gold-dim: rgba(201,168,76,0.12);
  --gold-bd:  rgba(201,168,76,0.25);
  --text-1: #E8E6DC;
  --text-2: rgba(232,230,220,0.55);
  --text-3: rgba(232,230,220,0.28);
  --border-hp: rgba(255,255,255,0.07);
  --sans-hp: 'DM Sans', system-ui, sans-serif;
  --disp-hp: 'Bebas Neue', Impact, sans-serif;
}

.pc-topbar {
  background: rgba(14,14,12,0.97);
  border-bottom: 0.5px solid var(--border-hp);
  padding: 0 36px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 600;
}

.pc-topbar-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.pc-logo {
  font-family: var(--disp-hp);
  font-size: 19px;
  letter-spacing: 2px;
  color: var(--text-1);
  text-decoration: none;
  white-space: nowrap;
}
.pc-logo span { color: var(--gold); }

.pc-breadcrumb {
  display: flex;
  align-items: center;
  gap: 7px;
  font-family: var(--sans-hp);
  font-size: 12px;
  color: var(--text-2);
  padding-left: 20px;
  border-left: 0.5px solid var(--border-hp);
}
.pc-breadcrumb a {
  color: var(--text-2);
  text-decoration: none;
  transition: color 0.15s;
}
.pc-breadcrumb a:hover { color: var(--text-1); }
.pc-breadcrumb .sep { color: var(--text-3); }
.pc-breadcrumb .cur { color: var(--text-1); }

.pc-topbar-right {
  display: flex;
  align-items: center;
  gap: 8px;
}
.pc-back {
  font-family: var(--sans-hp);
  font-size: 12px;
  color: var(--text-2);
  text-decoration: none;
  padding: 6px 14px;
  border-radius: 5px;
  border: 0.5px solid var(--border-hp);
  transition: all 0.15s;
}
.pc-back:hover { color: var(--text-1); border-color: rgba(255,255,255,0.2); }

/* ── Tool title strip — Bebas Neue like homepage sections ── */
.pc-tool-strip {
  background: var(--dark);
  border-bottom: 0.5px solid var(--border-hp);
  padding: 28px 36px 24px;
}
.pc-tool-strip-ey {
  font-family: var(--sans-hp);
  font-size: 10px;
  letter-spacing: 2.5px;
  text-transform: uppercase;
  color: var(--gold);
  margin-bottom: 6px;
}
.pc-tool-strip-title {
  font-family: var(--disp-hp);
  font-size: 44px;
  letter-spacing: 2px;
  line-height: 0.95;
  color: var(--text-1);
  margin-bottom: 8px;
}
.pc-tool-strip-title span { color: var(--gold); }
.pc-tool-strip-sub {
  font-family: var(--sans-hp);
  font-size: 13px;
  color: var(--text-2);
}
</style>
</head>
<body class="tool-page">

<nav class="nav">
  <a class="nav-logo" href="/">PRODUCTION<span>.</span>CENTRAL</a>
  <div class="nav-links">
    <a class="nav-link" href="/forum.php">Forum</a>
    <a class="nav-link" href="/#education">Education</a>
    <a class="nav-link" href="/#reference">Reference</a>
    <a class="nav-link" href="/#technology">Technology</a>
    <a class="nav-link active" href="/tools.php">Tools</a>
    <a class="nav-link" href="/#life">Life</a>
    <a class="nav-link store" href="/#store">Store</a>
  </div>
  <div class="nav-right">
<?php if ($_user_id): ?>
    <a class="nav-link" href="/myprofile.php"><?php echo htmlspecialchars($_username); ?></a>
    <a class="btn-signin" href="/logout.php">Log out</a>
<?php else: ?>
    <a class="btn-signin" href="/login.php">Sign in</a>
    <a class="btn-join" href="/register.php">Join free →</a>
<?php endif; ?>
  </div>
  <button class="nav-hamburger" aria-label="Open menu" onclick="toggleMobileNav()">
    <span></span><span></span><span></span>
  </button>
</nav>

<div class="mobile-nav" id="mobile-nav">
  <div class="mobile-nav-inner">
    <a class="mobile-nav-link" href="/forum.php">Forum</a>
    <a class="mobile-nav-link" href="/tools.php" style="color:var(--gold)">← Back to Tools</a>
    <div class="mobile-nav-divider"></div>
<?php if ($_user_id): ?>
    <a class="mobile-nav-link" href="/myprofile.php">My Profile</a>
    <a class="mobile-nav-link" href="/logout.php">Log out</a>
<?php else: ?>
    <a class="mobile-nav-link" href="/login.php">Sign in</a>
    <a class="btn-gold-lg" href="/register.php" style="margin-top:8px;text-align:center">Join free →</a>
<?php endif; ?>
  </div>
</div>
<div class="mobile-nav-overlay" id="mobile-nav-overlay" onclick="toggleMobileNav()"></div>
<script>
function toggleMobileNav(){
  document.getElementById('mobile-nav').classList.toggle('open');
  document.getElementById('mobile-nav-overlay').classList.toggle('open');
  document.body.classList.toggle('nav-open');
}
</script>

');
  c.push('<div id="ui">');
  c.push('<span id="inf">Production Central</span>');
  c.push('<span id="hnt">F = fullscreen</span>');
  c.push('<div><button class="btn" id="fsb">Fullscreen</button> <button class="btn" onclick="window.close()">Close</button></div>');
  c.push('</div>');
  c.push('<div id="pc" style="position:relative"><svg id="svg" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:100%;display:block"></svg><canvas id="sc" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none"></canvas></div>');
  c.push('<scr'+'ipt>');
  c.push('var t,ui=document.getElementById("ui");');
  c.push('function show(){ui.classList.remove("hidden");clearTimeout(t);t=setTimeout(function(){ui.classList.add("hidden");},3000);}');
  c.push('document.addEventListener("mousemove",show);show();');
  c.push('function fs(){if(!document.fullscreenElement){document.documentElement.requestFullscreen();}else{document.exitFullscreen();}}');
  c.push('document.getElementById("fsb").onclick=fs;');
  c.push('document.addEventListener("keydown",function(e){if(e.key=="f"||e.key=="F")fs();});');
  c.push('window.addEventListener("message",function(e){');
  c.push('  if(!e||!e.data)return;');
  c.push('  var d=e.data;');
  c.push('  if(d.type=="label"&&d.text)document.getElementById("inf").textContent=d.text;');
  c.push('  if(d.type=="pattern"){');
  c.push('    var s=document.getElementById("svg");');
  c.push('    if(!s)return;');
  c.push('    s.innerHTML=d.body||"";');
  c.push('    if(d.vx!=null)s.setAttribute("viewBox",d.vx+" "+d.vy+" "+d.vw+" "+d.vh);');
  c.push('  }');
  c.push('  if(d.type=="fullscreen")fs();');
  c.push('  if(d.type=="sweep"){');
  c.push('    var sc=document.getElementById("sc");');
  c.push('    var sv=document.getElementById("svg");');
  c.push('    if(!sc||!sv)return;');
  c.push('    var W=sv.clientWidth||window.innerWidth;');
  c.push('    var H=sv.clientHeight||window.innerHeight;');
  c.push('    if(sc.width!==W)sc.width=W;');
  c.push('    if(sc.height!==H)sc.height=H;');
  c.push('    var ctx=sc.getContext("2d");');
  c.push('    ctx.clearRect(0,0,W,H);');
  c.push('    if(d.posH<0){return;}');  
  c.push('    var w=d.weight||3;');
  c.push('    ctx.save();');
  c.push('    ctx.shadowBlur=0;');
  c.push('    ctx.strokeStyle="#00d4ff";ctx.lineWidth=w;');
  c.push('    ctx.beginPath();ctx.moveTo(0,d.posH*H);ctx.lineTo(W,d.posH*H);ctx.stroke();');
  c.push('    ctx.beginPath();ctx.moveTo(d.posV*W,0);ctx.lineTo(d.posV*W,H);ctx.stroke();');
  c.push('    ctx.restore();');
  c.push('  }');
  c.push('});');
  c.push('window.addEventListener("load",function(){if(window.opener)window.opener.postMessage({type:"output-ready"},"*");});');
  c.push('<'+'/script></body></html>');
  return c.join('');
}

function stpFillOutputWindow(pw, body, W, H, vx, vy, vw, vh, outW, outH, label, index, total) {
  if (!pw || pw.closed) return;
  window._stpPopoutWin = pw;

  var html = stpGetPopupHTML();
  var blob = new Blob([html], {type: 'text/html'});
  var url = URL.createObjectURL(blob);
  pw.location.href = url;

  // Poll until the popup is ready then push the pattern
  // load event on cross-origin blob URLs is unreliable
  var attempts = 0;
  var pushInterval = setInterval(function() {
    attempts++;
    try {
      // Check if the popup has our svg element — means it loaded
      var svg = pw.document && pw.document.getElementById('svg');
      if (svg) {
        clearInterval(pushInterval);
        URL.revokeObjectURL(url);
        pw.postMessage({ type: 'label', text: label }, '*');
        var msg = { type: 'pattern', body: body, W: W, H: H, vx: vx, vy: vy, vw: vw, vh: vh };
        pw.postMessage(msg, '*');
        if (window._stpChannel) window._stpChannel.postMessage(msg);
        return;
      }
    } catch(e) {}
    // Also try postMessage regardless after a delay — popup will handle when ready
    if (attempts === 5) {
      pw.postMessage({ type: 'label', text: label }, '*');
      var msg2 = { type: 'pattern', body: body, W: W, H: H, vx: vx, vy: vy, vw: vw, vh: vh };
      pw.postMessage(msg2, '*');
    }
    if (attempts > 20) clearInterval(pushInterval);
  }, 200);
}

// Listen for popup ready signal
window.addEventListener('message', function(e) {
  if (e.data && e.data.type === 'output-ready') {
    // A popup is ready — push current pattern to all outputs
    setTimeout(stpPushToPopout, 100);
  }
});

function stpPushToPopout() {
  try {
    const { body, W, H } = buildStandalonePatternSVG();
    const nOut  = parseInt(document.getElementById('stp-popout-outputs')?.value) || 1;
    const slice = document.getElementById('stp-popout-slice')?.value || 'h';

    window._stpOutputs = window._stpOutputs.filter(w => w && !w.closed);

    window._stpOutputs.forEach((pw, i) => {
      if (!pw || pw.closed) return;
      const vx = slice === 'h' ? Math.round(i * W / nOut) : 0;
      const vy = slice === 'v' ? Math.round(i * H / nOut) : 0;
      const vw = slice === 'h' ? Math.round(W / nOut) : W;
      const vh = slice === 'v' ? Math.round(H / nOut) : H;
      const msg = { type: 'pattern', body, W, H, vx, vy, vw, vh, sliceW: vw, sliceH: vh, outputIndex: i };
      pw.postMessage(msg, '*');
      if (window._stpChannel) window._stpChannel.postMessage(msg);
    });

    stpUpdateOutputControls();
  } catch(e) { console.warn('Popout push error:', e); }
}

function stpUpdateOutputControls() {
  window._stpOutputs = (window._stpOutputs || []).filter(w => w && !w.closed);
  const n = window._stpOutputs.length;
  const fsBtn    = document.getElementById('stp-fs-all-btn');
  const closeBtn = document.getElementById('stp-close-all-btn');
  const countEl  = document.getElementById('stp-popout-count');
  if (fsBtn)    fsBtn.style.display    = n > 0 ? 'block' : 'none';
  if (closeBtn) closeBtn.style.display = n > 0 ? 'block' : 'none';
  if (countEl) {
    countEl.style.display = n > 0 ? 'inline' : 'none';
    countEl.style.color = 'var(--text3)';
    countEl.textContent = n === 1 ? '1 output open' : `${n} outputs open`;
  }
}

function stpFullscreenAll() {
  // Browsers only allow fullscreen from a user gesture inside that window
  // Send the message anyway (works if popup handles it on next user interaction)
  // and show a clear instruction
  var outputs = (window._stpOutputs || []).filter(function(w) { return w && !w.closed; });
  outputs.forEach(function(pw) {
    try { pw.postMessage({ type: 'fullscreen' }, '*'); } catch(e) {}
  });
  var countEl = document.getElementById('stp-popout-count');
  if (countEl) {
    var orig = countEl.textContent;
    var origColor = countEl.style.color;
    countEl.textContent = outputs.length > 1
      ? 'Click each output window then press F'
      : 'Click the output window then press F';
    countEl.style.color = 'var(--accent)';
    setTimeout(function() {
      countEl.textContent = orig;
      countEl.style.color = origColor;
    }, 4000);
  }
}

function stpCloseAll() {
  (window._stpOutputs || []).forEach(pw => { try { if (!pw.closed) pw.close(); } catch(e) {} });
  window._stpOutputs = [];
  stpUpdateOutputControls();
}

// ── PATTERN LOCK & CLEAR ──────────────────────────────────────────────────────

function stpIsLocked() {
  return document.getElementById('stp-locked')?.checked === true;
}

function stpToggleLock() {
  const cb  = document.getElementById('stp-locked');
  const btn = document.getElementById('stp-lock-btn');
  if (!cb || !btn) return;
  const locked = cb.checked;
  cb.checked = !locked;
  btn.classList.toggle('stp-pill-on', !locked);
  btn.innerHTML = (locked ? '🔓 Unlocked' : '🔒 Locked') +
    '<input type="checkbox" id="stp-locked" style="display:none"' + (locked ? '' : ' checked') + '>';
  // Update launch buttons in other tabs to reflect lock state
  stpUpdateLaunchBtnTitles();
  if (!locked) {
    stpShowPatternMsg('Pattern locked — other tabs cannot overwrite settings', 'var(--accent)');
  } else {
    stpShowPatternMsg('Pattern unlocked', 'var(--text2)');
  }
}

function stpUpdateLaunchBtnTitles() {
  const locked = stpIsLocked();
  const msg = locked ? 'Pattern is locked — unlock in Test Patterns tab first' : 'Open pattern in Test Patterns tab';
  // Update all launch buttons in other tabs
  document.querySelectorAll('[onclick*="launchLEDTestPattern"],[onclick*="launchTestPatternFromProj"]').forEach(btn => {
    btn.title = msg;
    btn.style.opacity = locked ? '0.5' : '1';
    btn.style.cursor = locked ? 'not-allowed' : 'pointer';
  });
}

function stpCheckLock(source) {
  if (!stpIsLocked()) return true; // not locked, allow
  // Switch to test pattern tab so user can see the lock message and unlock
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('tab-testpattern').classList.add('active');
  const tpBtn = [...document.querySelectorAll('.tab-btn')].find(b => b.onclick?.toString().includes('testpattern'));
  if (tpBtn) tpBtn.classList.add('active');
  stpShowPatternMsg('🔒 Pattern locked — click Unlocked to allow changes from ' + source, 'var(--accent2)');
  return false;
}

function stpShowPatternMsg(msg, color) {
  const el = document.getElementById('stp-pattern-msg');
  if (!el) return;
  el.textContent = msg;
  el.style.color = color || 'var(--accent)';
  el.style.opacity = '1';
  clearTimeout(el._msgTimer);
  el._msgTimer = setTimeout(() => { el.style.opacity = '0'; }, 4000);
}

function stpClearPattern() {
  // Reset everything to clean generic defaults
  // Switch to generic sub-tab
  stpSetSubtab('generic');

  // Pattern type and dimensions
  document.getElementById('stp-type').value = 'grid';
  document.getElementById('stp-width').value  = 1920;
  document.getElementById('stp-height').value = 1080;

  // Reset zones and blend
  document.getElementById('stp-projectors').value = 1;
  const blendEl = document.getElementById('stp-blend-px');
  if (blendEl) blendEl.value = 0;

  // Reset background to solid black
  document.querySelectorAll('.stp-swatch').forEach(s => s.style.borderColor = 'transparent');
  // Find and highlight the black swatch specifically
  document.querySelectorAll('.stp-swatch').forEach(s => {
    if (s.onclick && s.onclick.toString().includes("'#000000'")) {
      s.style.borderColor = '#00d4ff';
    }
  });
  const bgEl = document.getElementById('stp-bg');
  if (bgEl) bgEl.value = 'solid:#000000';
  // Hide gradient controls
  const gradControls = document.getElementById('stp-grad-controls');
  if (gradControls) gradControls.style.display = 'none';
  const gradActive = document.getElementById('stp-grad-active');
  if (gradActive) gradActive.value = '0';

  // Reset overlays: grid + crosshair on, rest off
  const defaults = {
    'stp-ov-grid': true, 'stp-ov-markers': false,
    'stp-ov-circles': false, 'stp-ov-hcircles': false,
    'stp-ov-diag': false, 'stp-ov-crosshair': true
  };
  Object.entries(defaults).forEach(([id, on]) => {
    const cb = document.getElementById(id);
    const pill = cb?.closest('label');
    if (cb) cb.checked = on;
    if (pill) pill.classList.toggle('stp-pill-on', on);
  });

  // Reset grid and line weight
  const vcols = document.getElementById('stp-vcols'); if (vcols) vcols.value = 16;
  const hrows = document.getElementById('stp-hrows'); if (hrows) hrows.value = 9;
  const lw = document.getElementById('stp-lineweight'); if (lw) lw.value = 2;

  stpUpdateBlendCard();
  calcStandalonePattern();
  stpShowPatternMsg('Pattern cleared', 'var(--text2)');
}

function stpResetToDefaults(context) {
  stpClearPattern();
}


// ═══════════════════════════════════════════════════════════════════════════
// SWEEP LINE ANIMATION
// ═══════════════════════════════════════════════════════════════════════════

var _sweepActive  = false;
var _sweepRAF     = null;
var _sweepPosH    = 0;   // 0-1 normalised vertical position (H line)
var _sweepPosV    = 0;   // 0-1 normalised horizontal position (V line)
var _sweepLastTs  = null;
var _sweepConfig  = { weight: 3, speed: 3 }; // speed 1-10

function stpToggleSweep(pill) {
  const cb = document.getElementById('stp-ov-sweep');
  cb.checked = !cb.checked;
  _sweepActive = cb.checked;
  pill.classList.toggle('stp-pill-on', _sweepActive);
  const adjCard = document.getElementById('stp-adj-sweep');
  if (adjCard) adjCard.style.display = _sweepActive ? 'flex' : 'none';
  const canvas = document.getElementById('stp-sweep-canvas');
  if (canvas) canvas.style.display = _sweepActive ? 'block' : 'none';
  if (_sweepActive) {
    _sweepLastTs = null;
    _sweepRAF = requestAnimationFrame(stpSweepLoop);
  } else {
    if (_sweepRAF) { cancelAnimationFrame(_sweepRAF); _sweepRAF = null; }
    // Clear canvas
    if (canvas) { const ctx = canvas.getContext('2d'); ctx.clearRect(0, 0, canvas.width, canvas.height); }
    // Tell popouts to hide sweep
    stpSweepSendToPopouts(-1, -1);
  }
}

function stpSweepUpdateConfig() {
  _sweepConfig.weight = parseInt(document.getElementById('stp-sweep-weight')?.value) || 3;
  _sweepConfig.speed  = parseInt(document.getElementById('stp-sweep-speed')?.value)  || 3;
}

function stpSweepLoop(ts) {
  if (!_sweepActive) return;

  // Advance positions — ts may be undefined on first call, handle gracefully
  if (ts && _sweepLastTs !== null) {
    const dt = Math.min(ts - _sweepLastTs, 100);
    // speed 1 = ~20s full cycle, speed 10 = ~2s full cycle
    const cyclesPerSec = _sweepConfig.speed * 0.05;
    const delta = (dt / 1000) * cyclesPerSec;
    _sweepPosH = (_sweepPosH + delta) % 1;
    _sweepPosV = (_sweepPosV + delta) % 1;
  }
  _sweepLastTs = ts || performance.now();

  // Draw on preview canvas — size it to match the container exactly
  const canvas = document.getElementById('stp-sweep-canvas');
  const container = canvas ? canvas.parentElement : null;
  if (canvas && container) {
    const W = container.offsetWidth;
    const H = container.offsetHeight;
    // Only resize if needed (resizing clears the canvas)
    if (canvas.width !== W)  canvas.width  = W;
    if (canvas.height !== H) canvas.height = H;
    if (W > 0 && H > 0) {
      const ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, W, H);
      stpDrawSweepLines(ctx, W, H, _sweepPosH, _sweepPosV, _sweepConfig.weight);
    }
  }

  // Send normalised positions to popouts
  stpSweepSendToPopouts(_sweepPosH, _sweepPosV);

  _sweepRAF = requestAnimationFrame(stpSweepLoop);
}



function stpDrawSweepLines(ctx, W, H, posH, posV, weight) {
  const lw = parseFloat(document.getElementById('stp-sweep-weight')?.value) || weight;
  ctx.save();
  ctx.shadowBlur  = 0;
  ctx.strokeStyle = '#00d4ff';
  ctx.lineWidth   = lw;
  // Horizontal sweep line (moves top→bottom)
  ctx.beginPath();
  ctx.moveTo(0, posH * H);
  ctx.lineTo(W, posH * H);
  ctx.stroke();
  // Vertical sweep line (moves left→right)
  ctx.beginPath();
  ctx.moveTo(posV * W, 0);
  ctx.lineTo(posV * W, H);
  ctx.stroke();
  ctx.restore();
}

function stpSweepSendToPopouts(posH, posV) {
  const outputs = (window._stpOutputs || []).filter(w => w && !w.closed);
  if (!outputs.length) return;
  const msg = { type: 'sweep', posH, posV, weight: _sweepConfig.weight };
  outputs.forEach(pw => {
    try { pw.postMessage(msg, '*'); } catch(e) {}
  });
  if (window._stpChannel) {
    try { window._stpChannel.postMessage(msg); } catch(e) {}
  }
}

</script>
<script>

document.addEventListener('DOMContentLoaded', function() {
  _registerProjectionListeners();
  try { calcStandalonePattern(); } catch(e) { console.error('calcStandalonePattern:', e); }
});

</script>

</body>
</html>
