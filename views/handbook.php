<?php /* views/handbook.php — Student Handbook */ ?>

<link rel="stylesheet" href="/static/style2.css?v=recov1">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&display=swap" rel="stylesheet">

<style>
  /* Remove outer scrollbar and fix size to viewport */
  html, body {
    margin: 0 !important;
    padding: 0 !important;
    height: 100vh !important;
    max-height: 100vh !important;
    overflow: hidden !important;
  }
  
  #app {
    width: 100vw !important;
    height: calc(100vh - 70px) !important;
    max-height: calc(100vh - 70px) !important;
    margin-top: 70px !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    box-sizing: border-box !important;
  }
  
  .page-wrap {
    min-height: 100% !important;
    box-sizing: border-box !important;
    padding: 20px;
  }
  
  .handbook-container {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
  }
  
  .handbook-title {
    font-size: 2rem;
    color: #333;
    margin-bottom: 20px;
  }
  
  .handbook-iframe-wrapper {
    position: relative;
    width: 100%;
    max-width: 700px;
    margin: 0 auto;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
  }
  
  .handbook-iframe-wrapper iframe {
    width: 100%;
    height: 425px;
    border: none;
    display: block;
  }
  
  /* Responsive design */
  @media (max-width: 768px) {
    .handbook-title {
      font-size: 1.5rem;
    }
    
    .handbook-iframe-wrapper {
      max-width: 100%;
    }
    
    .handbook-iframe-wrapper iframe {
      height: 350px;
    }
  }
  
  @media (max-width: 480px) {
    .page-wrap {
      padding: 10px;
    }
    
    .handbook-title {
      font-size: 1.25rem;
    }
    
    .handbook-iframe-wrapper iframe {
      height: 300px;
    }
  }
</style>

<div class="page-wrap">
  <div class="handbook-container">
    <h1 class="handbook-title">คู่มือนักเรียน</h1>
    
    <div class="handbook-iframe-wrapper">
      <iframe 
        src="https://online.anyflip.com/ksbst/msej/index.html" 
        seamless="seamless" 
        scrolling="no" 
        frameborder="0" 
        allowtransparency="true" 
        allowfullscreen="true"
        title="คู่มือนักเรียน">
      </iframe>
    </div>
    
    <div style="margin-top: 20px;">
      <a href="https://online.anyflip.com/ksbst/msej/index.html" 
         target="_blank" 
         rel="noopener noreferrer"
         style="color: #0066cc; text-decoration: none; font-size: 0.9rem;">
        เปิดในหน้าต่างใหม่ →
      </a>
    </div>
  </div>
</div>
