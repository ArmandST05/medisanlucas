
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
 <html>
 <head>
 <link rel="stylesheet" type="text/css" href="storage_data/prescripition/style.css"/>
 <style type="text/css">
     @page {
         margin: 0px !important;
     }
     @font-face {
         font-family: "Brush Script MT";           
         src: local("Brush Script MT"), url("plugins/dompdf/lib/fonts/BrushScriptMT.ttf") format("truetype");
         font-weight: normal;
         font-style: normal;
     }    
     @font-face {
         font-family: "Calibri";           
         src: local("Calibri"), url("plugins/dompdf/lib/fonts/Calibri.ttf") format("truetype");
         font-weight: normal;
         font-style: normal;
     }     
     .title-bold{       
         font-family: "Calibri", Calibri; 
         font-weight:bold;
         font-size:12pt;
         color:#000000";
      
     }
 
     .title-content{       
         font-family: "Calibri", Calibri; 
         font-style:italic;
         font-weight:normal;
         font-size:11pt;
         color:#000000"; 
     }
 
     .italic-title{  
         font-family: "Brush Script MT", Brush Script MT;
         font-weight:normal;
         font-size:14pt;
     } 
 
     .span-content{       
         font-family: "Calibri", Calibri;   
         font-style:normal;
         font-weight:normal;
         font-size:8pt;
         color:#000000";
     }
 
     .prescription-content{       
         font-family: "Calibri", Calibri;   
         font-style:italic;
         font-weight:normal;
         font-size:9pt;
         color:#000000";
     }
 </style>
 </head>
 <body>
 <img style="position:absolute;top:1.14in;left:3.63in;width:2.07in;height:4.55in" src="storage_data/prescription/ri_1.png" />
 <img style="position:absolute;top:5.17in;left:6.49in;width:2.52in;height:0.47in" src="storage_data/prescription/vi_1.png" />
 <img style="position:absolute;top:5.23in;left:6.50in;width:2.52in;height:0.35in" src="storage_data/prescription/ri_2.png" />
 <img style="position:absolute;top:4.55in;left:6.1in;width:1.4in;height:1in" src="assets/blank-space.png" />
 <div style="position:absolute;top:5.25in;left:5.71in;width:2.26in;line-height:0.12in;">
     <span style="font-style:normal;font-weight:bold;font-size:8pt;font-family:Calibri;color:#000000">_____________________________________</span>
 </div>
 <div style="position:absolute;top:5.40in;left:6.61in;width:1.59in;line-height:0.12in;">
     <span style="font-style:normal;font-weight:bold;font-size:8pt;font-family:Calibri;color:#000000">Firma del Médico </span>
 </div>
 <img style="position:absolute;top:1.38in;left:0.19in;width:0.72in;height:0.28in" src="storage_data/prescription/ri_3.png" />
 <div style="position:absolute;top:1.41in;left:0.30in;width:2.5in;line-height:0.18in;">
     <span class="title-bold">Fecha: </span><span class="title-content">[reservation-date_format]</span><br/>
 </div>
 <img style="position:absolute;top:1.70in;left:6.05in;width:0.85in;height:0.19in" src="storage_data/prescription/ri_4.png" />
 <div style="position:absolute;top:1.73in;left:6.16in;width:2.5in;line-height:0.18in;">
 <span class="title-bold">Edad:</span><span class="title-content">[patient-age]</span>
 </div>
 <img style="position:absolute;top:1.70in;left:0.19in;width:1.05in;height:0.28in" src="storage_data/prescription/ri_5.png" />
 <div style="position:absolute;top:1.74in;left:0.30in;width:6in;line-height:0.18in;">
 <span class="title-bold">Nombre: </span>
 <span class="title-content">[patient-name]</span>
 </div>
 <img style="position:absolute;top:1.60in;left:0.28in;width:7.60in;height:0.05in" src="storage_data/prescription/vi_2.png" />
 <img style="position:absolute;top:2.06in;left:6.04in;width:1.05in;height:0.28in" src="storage_data/prescription/ri_6.png" />
 <div style="position:absolute;top:2.10in;left:6.15in;width:3in;line-height:0.18in;">
 <span class="title-bold">Alergias:</span>
 <span class="prescription-content">[record_section-1]</span>
 </div>
 <img style="position:absolute;top:2.08in;left:0.19in;width:1.05in;height:0.28in" src="storage_data/prescription/ri_7.png" />
 <div style="position:absolute;top:2.11in;left:0.30in;width:15in;line-height:0.18in;">
 <span class="title-bold">DX: </span>
 </div>
 <div style="position:absolute;top:2.11in;left:0.60in;width:5.5in;line-height:0.18in;">
 <span class="prescription-content">[diagnostic-details]<br>[diagnostic-diagnostic_observations]</span>
 </div>
 <div style="position:absolute;top:3in;left:0.30in;width:15in;line-height:0.18in;">
 <span class="prescription-content">[medicine-details]</span>
 </div>
 <img style="position:absolute;top:1.99in;left:0.26in;width:7.6in;height:0.03in" src="storage_data/prescription/vi_3.png" />
 <img style="position:absolute;top:0.01in;left:0.00in;width:8.4in;height:1.72in" src="storage_data/prescription/ri_8.png" />
 <img style="position:absolute;top:0.60in;left:5.11in;width:3.92in;height:0.99in" src="storage_data/prescription/ri_9.png" />
 <div style="position:absolute;top:0.60in;left:5.2in;width:2.65in;line-height:0.18in;text-align:center;">
 <span class="italic-title" style="font-size:14pt;color:#000000">[medic-name]</span>
 </div>
 <div style="position:absolute;top:0.88in;left:5.2in;width:2.65in;line-height:0.15in;text-align:center;">
 <span class="italic-title" style="font-weight:normal;font-size:12pt;color:#000000">[medic-category_name]</span><br/></div>
 <div style="position:absolute;top:1.12in;left:5.2in;width:2.65in;line-height:0.15in;text-align:center;">
 <span class="italic-title" style="font-weight:normal;font-size:12pt;color:#000000">Céd.Prof. [medic-professional_license]</span><br/></div>
 <div style="position:absolute;top:1.36in;left:5.2in;width:2.65in;line-height:0.15in;text-align:center;">
 <span class="italic-title" style="font-weight:normal;font-size:12pt;color:#000000">[medic-study_center]</span><br></div>
 
 <img style="position:absolute;top:0.19in;left:1.35in;width:1.50in;height:0.75in" src="storage_data/prescription/ri_10.png" />
 <img style="position:absolute;top:2.34in;left:7.73in;width:1.42in;height:2.10in" src="storage_data/prescription/ri_11.png" />
 <div style="position:absolute;top:2.37in;left:7in;width:1.5in;line-height:0.14in;"><span class="span-content">Peso: [vital_sign-10]Kg</span></div>
 <div style="position:absolute;top:2.62in;left:7in;width:1.5in;line-height:0.14in;"><span class="span-content">Talla: [vital_sign-86] m</span></div>
 <div style="position:absolute;top:2.87in;left:7in;width:1.5in;line-height:0.14in;"><span class="span-content">IMC: [vital_sign-11]</div>
 <div style="position:absolute;top:3.12in;left:7in;width:1.5in;line-height:0.14in;"><span class="span-content">T/A: [vital_sign-85] mmHg</span></div>
 <div style="position:absolute;top:3.37in;left:7in;width:1.5in;line-height:0.14in;"><span class="span-content">Tº: [vital_sign-1]</span></div>
 <div style="position:absolute;top:3.62in;left:7in;width:1.5in;line-height:0.14in;"><span class="span-content">FC: [vital_sign-5] lpm</span></div>
 <div style="position:absolute;top:3.87in;left:7in;width:1.5in;line-height:0.14in;"><span class="span-content">FR: [vital_sign-2] rpm</span></div>
 <div style="position:absolute;top:4.12in;left:7in;width:1.5in;line-height:0.14in;"><span class="span-content">SO2: [vital_sign-9] %</span></div>
 <img style="position:absolute;top:5.57in;left:2.00in;width:2.22in;height:0.31in" src="storage_data/prescription/vi_4.png" />
 <img style="position:absolute;top:5.63in;left:2.00in;width:2.21in;height:0.19in" src="storage_data/prescription/ri_12.png" />
 <div style="position:absolute;top:5.66in;left:2.11in;width:1.79in;line-height:0.14in;"><span class="span-content">[configuration-email]</span></div>
 <img style="position:absolute;top:5.55in;left:4.23in;width:5.46in;height:0.35in" src="storage_data/prescription/ci_1.png" />
 <img style="position:absolute;top:5.61in;left:4.24in;width:5.45in;height:0.23in" src="storage_data/prescription/ri_13.png" />
 <div style="position:absolute;top:5.63in;left:4.35in;width:4.57in;line-height:0.14in;"><span class="span-content">[configuration-address]</span><span class="span-content"> </span><br/></SPAN></div>
 <img style="position:absolute;top:5.57in;left:0.35in;width:1.35in;height:0.35in" src="storage_data/prescription/vi_5.png" />
 <img style="position:absolute;top:5.63in;left:0.36in;width:1.35in;height:0.23in" src="storage_data/prescription/ri_14.png" />
 <div style="position:absolute;top:5.66in;left:0.46in;width:1.01in;line-height:0.14in;">
 <span class="span-content">Tel. [configuration-phone]</span></div>
 <img style="position:absolute;top:5.58in;left:0.26in;width:7.6in;height:0.05in" src="storage_data/prescription/vi_6.png" />
 <img style="position:absolute;top:5.63in;left:0.29in;width:0.17in;height:0.17in" src="storage_data/prescription/ri_15.png" />
 <img style="position:absolute;top:5.67in;left:1.98in;width:0.11in;height:0.10in" src="storage_data/prescription/ri_16.png" />
 <img style="position:absolute;top:5.57in;left:4.14in;width:0.28in;height:0.28in" src="storage_data/prescription/ri_17.png" />
 </html>