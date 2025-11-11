import jsPDF from 'jspdf'
import { imgUrlToDataUrl } from './imgUrlToDataUrl.js'
import logo from "../assets/favicon.jpg"
import { formatCurrency, formatDate, capitalizeFirst } from './utils.js'

export const downloadInvoice = async(invoice,subtotal,total) => {
  const doc = new jsPDF()
  
  // Configuration
  const pageWidth = doc.internal.pageSize.getWidth()
  const pageHeight = doc.internal.pageSize.getHeight()
  let yPos = 16
  let margin= 15

  // Divider line
  const divider = (lineWidth,x1,y1,x2,y2)=>{
    doc.setDrawColor(220, 220, 220);
    doc.setLineWidth(lineWidth);
    doc.line(x1,y1,x2,y2);
  }

  // Pied de page
  const footer = ()=>{
    // divider
    divider(0.1,margin, pageHeight - 25, pageWidth - margin, pageHeight - 25)

    doc.setFontSize(8)
    doc.setTextColor(107, 114, 128)
    doc.text('Thank you for your trust! For any questions, please contact us at commandes@daqauto.com', pageWidth / 2, pageHeight - 20, { align: 'center' })
    doc.text('DAQ AUTO Marketplace - Your trusted partner in global vehicle sourcing.', pageWidth / 2, pageHeight - 15, { align: 'center' })
    
    doc.setTextColor(254, 121, 0)
    doc.setFont('helvetica', 'bold')
    doc.text('Invoice generated on DaqAuto.com ', pageWidth / 2, pageHeight - 10, { align: 'center' })
  }
  
  // En-tête
  const header =()=>{
  // En-tête
  doc.setFillColor(255, 255, 255)
  doc.rect(0, 0, pageWidth, 40, 'F')
  
  doc.setTextColor(0, 0, 0)
  doc.setFontSize(14)
  doc.addImage(logo, margin, 12, 8, 8)
  doc.setFont('helvetica', 'bold')
  doc.text('INVOICE', margin+10, yPos)
  
  doc.setFontSize(7)
  doc.setTextColor(107, 114, 128)
  doc.setFont('helvetica', 'normal')
  doc.text('DAQ AUTO Marketplace',margin+10, yPos+4)
  

  // Numéro de facture
  margin = 20
  doc.setFillColor(254, 121, 0)
  doc.setDrawColor(254, 121, 0);
  doc.roundedRect(pageWidth - margin -30, yPos-4, 30, 6,1,1, 'FD')
  
  doc.setTextColor(255, 255, 255)
  doc.setFont('helvetica', 'bold')
  doc.setFontSize(9)
  doc.text(invoice.number || 'INV-XXXX-XXX', pageWidth - margin-15, yPos, { align: 'center' })

  // Dates (sous le n°)
  doc.setTextColor(0, 0, 0)
  doc.setFontSize(8)
  doc.setFont('helvetica', 'bold')
  doc.text(`Date: ${formatDate(invoice.date)}`, pageWidth - margin-7, yPos+5, { align: 'right' })
  doc.text(`Due date: ${formatDate(invoice.dueDate)}`, pageWidth - margin-1, yPos+9, { align: 'right' })

  divider(0.2,margin-5, yPos+15,  pageWidth-15 , yPos+15)
  }

// Sous-entête
const subHeader =()=>{
  /// Fin entête ----- Debut sous entête
  yPos += 24
  margin = 15
  doc.setFontSize(9) 
  doc.setFont('helvetica', 'bold')
  doc.setTextColor(107, 114, 128)
  doc.text('Seller :', margin, yPos)
  
  yPos += 3
  // Informations entreprise
  doc.addImage(logo, 'PNG', margin, yPos, 6, 6)
  doc.setFontSize(9)
  doc.setFont('helvetica', 'normal')
  doc.text('DAQ AUTO', margin+8, yPos+5)
  doc.setFontSize(7)
  doc.setTextColor(107, 114, 128)
  doc.text('Abidjan, Côte d\'Ivoire', margin, yPos +10 )
  doc.text('+225 01 53 67 60 62', margin, yPos + 13)
  doc.text('commandes@daqauto.com', margin, yPos + 16)
  doc.text('(USCC): 91310000MA1K4T123X', margin, yPos + 19)
  
  // Infos client
  margin=50
  doc.setFontSize(9) 
  doc.setFont('helvetica', 'bold')
  doc.text('Buyer :', pageWidth - margin, yPos-3, { align: 'left' })
  doc.setFont('helvetica', 'normal')
  doc.text(invoice.client.name || 'Customer name', pageWidth - margin, yPos+4,{ align: 'left' })
  doc.setFontSize(7)
  doc.setTextColor(107, 114, 128)
  doc.text(invoice.client.address || 'Customer address', pageWidth - margin, yPos + 7,{ align: 'left' })
  doc.text(invoice.client.phone || '+225 XX XX XX XX XX', pageWidth - margin, yPos + 10,{ align: 'left' })
  doc.text(invoice.client.email || 'email@exemple.com', pageWidth - margin, yPos + 13,{ align: 'left' })
}
  
// Section des articles
const itemsTable =()=>{
  // Tableau des articles
  yPos += 25
  margin = 15
  doc.setFontSize(6)
  doc.setFont('helvetica', 'bold')
  doc.setTextColor(0,0,0)
  doc.setFillColor(243, 244, 246) // Gris clair
  doc.rect(margin, yPos, pageWidth - margin-15, 8, 'F')
  
  doc.text('NO.', margin + 5, yPos + 5, { align: 'center' })
  doc.text('Product Type', margin + 15, yPos + 5, { align: 'center' })
  doc.text('DESCRIPTION', margin + 50, yPos + 5, { align: 'center' })
  doc.text('Trim / Vehicle Model', margin + 90, yPos + 5, { align: 'center' })
  doc.text('VIN', margin + 110, yPos + 5, { align: 'center' })
  doc.text('Stock Number', margin + 130, yPos + 5, { align: 'center' })
  doc.text('Color', margin + 145, yPos + 5, { align: 'center' })
  doc.text('QTY',margin + 152, yPos + 5, { align: 'center' })
  doc.text('UNIT PRICE', margin +163, yPos + 5, { align: 'center' })
  doc.text('TOTAL', margin + 175, yPos + 5, { align: 'center' })
  
  yPos += 12
  
  // Articles - Use product name from products list
  doc.setFont('helvetica', 'normal')
  for (let index = 0; index < invoice.items.length; index++) {
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }

    doc.text(String(index+1),margin + 5, yPos , { align: 'center' })
    doc.text( invoice.items[index].product_type || "N/A",margin + 15, yPos, { align: 'center' })
    doc.text(invoice.items[index].product_name || "N/A",margin + 50, yPos, { align: 'center' })
    doc.text(invoice.items[index].trim_number || "N/A",margin + 90, yPos, { align: 'center' })
    doc.text(invoice.items[index].vin || "N/A",margin + 110, yPos, { align: 'center' } )
    doc.text(invoice.items[index].stock_number || "N/A",margin + 130, yPos, { align: 'center' } )
    doc.text(invoice.items[index].color || "N/A", margin + 145, yPos, { align: 'center' })
    doc.text(String(invoice.items[index].quantity), margin + 152, yPos, { align: 'center' })
    doc.text(formatCurrency(invoice.items[index].price), margin +163, yPos, { align: 'center' })
    doc.text(formatCurrency(invoice.items[index].quantity * invoice.items[index].price), margin + 175, yPos, { align: 'center' })
    
    yPos += 7

  }
  // divider
  divider(0.1,margin, yPos,  pageWidth-15 , yPos)
}

// Section des totaux
const totalsSection =()=>{  
  yPos += 10
   if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
  // Totaux
  doc.setFont('helvetica', 'normal')
  doc.setTextColor(107, 114, 128)
  doc.setFontSize(7)
  doc.text('Subtotal:', pageWidth - 80, yPos)
  doc.text(formatCurrency(subtotal), pageWidth - 25, yPos, { align: 'right' })
  divider(0.1,pageWidth - 80, yPos+2,  pageWidth-15 , yPos+2)
  
  yPos += 10
  doc.text("Shipping / Handling :", pageWidth - 80, yPos)
  doc.text("N/A", pageWidth - 25, yPos, { align: 'right' })
  divider(0.1,pageWidth - 80, yPos+2,  pageWidth-15 , yPos+2)

  yPos += 10
  doc.text("Insurance :", pageWidth - 80, yPos)
  doc.text("N/A", pageWidth - 25, yPos, { align: 'right' })
  divider(0.1,pageWidth - 80, yPos+2,  pageWidth-15 , yPos+2)

  yPos += 10
  doc.text("Sea Shipping :", pageWidth - 80, yPos)
  doc.text("N/A", pageWidth - 25, yPos, { align: 'right' })
  divider(0.1,pageWidth - 80, yPos+2,  pageWidth-15 , yPos+2)
  
  yPos += 10
   if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
  doc.setDrawColor(254, 121, 0);
  doc.setFillColor(254, 121, 0)
  doc.roundedRect(pageWidth - 90, yPos - 5, 75, 10,2, 2, 'FD')
  
  doc.setTextColor(255, 255, 255)
  doc.setFont('helvetica', 'bold')
  doc.setFontSize(11)
  doc.text('TOTAL:', pageWidth - 85, yPos + 2)
  doc.text(formatCurrency(total), pageWidth - 20, yPos + 2, { align: 'right' })
}

// Bank Information
const bankInfos = ()=>{
  yPos += 15
   if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
  
  // Informations Bank
  doc.setFillColor(254, 243, 199)
  doc.setDrawColor(254, 121, 0); // Orange clair
  doc.roundedRect(margin, yPos, pageWidth - 30, 38,1,1, 'FD')
  
  doc.setFontSize(9)
  doc.setFont('helvetica', 'bold')
  doc.setTextColor(254, 121, 0) // Orange foncé
  doc.text(' Bank Information', margin+5, yPos + 7)
  
  doc.setTextColor(0, 0, 0)
  doc.setFontSize(8)
  doc.setFont('helvetica', 'normal')
  doc.text( 'Beneficiary Name :', 25, yPos + 13)
  doc.setFont('helvetica', 'bold')
  doc.text( 'DAQ AUTO CO., LTD', 50, yPos + 13, )
  doc.setFont('helvetica', 'normal')
  doc.text('Bank Name :', 25, yPos + 18)
  doc.setFont('helvetica', 'bold')
  doc.text( ' Bank of China, Chongqing Branch', 50, yPos + 18, )
  doc.setFont('helvetica', 'normal')
  doc.text('Account Number :', 25, yPos + 23)
  doc.setFont('helvetica', 'bold')
  doc.text( '123 456 7890', 50, yPos + 23, )
  doc.setFont('helvetica', 'normal')
  doc.text('SWIFT Code :', 25, yPos + 28)
  doc.setFont('helvetica', 'bold')
  doc.text( 'BKCHCNBJ600', 50, yPos + 28, )
  doc.setFont('helvetica', 'normal')
  doc.text('Bank Address :', 25, yPos + 33)
  doc.setFont('helvetica', 'bold')
  doc.text( 'No. 123 Jiangbei District, Chongqing, China', 50, yPos + 33, )

  // divider
  divider(0.1,margin, yPos+43,  pageWidth-15 , yPos+43)
}

// Terms & Conditions
const termsAndConditions = ()=>{
  yPos += 55
   if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }

    doc.setTextColor(107, 114, 128)
    doc.setFontSize(9)
    doc.setFont('helvetica', 'bold')
    doc.text('Terms & Conditions ', margin, yPos-2)
    doc.setFont('helvetica', 'normal')
    doc.text("-", margin, yPos+5, {align:"center"})
    doc.text('Incoterm : CIF / FOB / EXW [Specify port ]', margin+2, yPos+5)
    doc.text("-", margin, yPos+10, {align:"center"})
    doc.text('Payment Terms : T/T — 100% payment received before shipment', margin+2, yPos+10)
    doc.text("-", margin, yPos+15, {align:"center"})
    doc.text('Production Time : [e.g. 15–20 working days after deposit ]', margin+2, yPos+15)
    doc.text("-", margin, yPos+20, {align:"center"})
    doc.text('Estimated Delivery : [Insert ] ', margin+2, yPos+20)
    doc.text("-", margin, yPos+25, {align:"center"})
    doc.text('Commercial invoice :This is the final commercial invoice for customs and payment confirmation.', margin+2, yPos+25)
    
    yPos += 35
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
    doc.setFontSize(9)
    doc.setTextColor(107, 114, 128)
    doc.setFont('helvetica', 'bold')
    doc.text('Shipping & Packaging ', margin, yPos-2)
    doc.setFont('helvetica', 'normal')
    doc.text("-", margin, yPos+5, {align:"center"})
    doc.text('Mode of Transport: [Sea / Air / Road / ]', margin+2, yPos+5)
    doc.text("-", margin, yPos+10, {align:"center"})
    doc.text('Port of Loading : [Insert ]', margin+2, yPos+10)
    doc.text("-", margin, yPos+15, {align:"center"})
    doc.text('Port of Destination : [Insert ]', margin+2, yPos+15)
    doc.text("-", margin, yPos+20, {align:"center"})
    doc.text('Packaging Type : [Export Standard]', margin+2, yPos+20)

    yPos += 33
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
    doc.setTextColor(107, 114, 128)
    doc.setFontSize(9)
    doc.setFont('helvetica', 'bold')
    doc.text('Declaration  ', margin, yPos-2)
    doc.setFont('helvetica', 'normal')
    doc.text('We hereby certify that the goods listed above are of Chinese origin and the details given are true and correct. ', margin, yPos+5)


  yPos += 15
   if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
  // Notes
  if (invoice.notes) {
    doc.setTextColor(107, 114, 128)
    doc.setFontSize(9)
    doc.setFont('helvetica', 'bold')
    doc.text('NOTES / CONDITIONS', margin, yPos)
    doc.setFont('helvetica', 'normal')
    
    
    const splitNotes = doc.splitTextToSize(invoice.notes, pageWidth - 40)
    doc.text(splitNotes, margin, yPos + 5)
    yPos += splitNotes.length * 5 + 10
  }
}

// signature
const signature = ()=>{
  yPos += 20
  doc.setTextColor(107, 114, 128)
  doc.setFontSize(9)
  doc.setFont('helvetica', 'normal')
  doc.text('Authorized Signature & Stamp ', pageWidth - margin - 60, yPos)
  doc.text('Name : [Authorized person ]', pageWidth - margin - 60, yPos + 6)
  doc.text('Signature : ___________________', pageWidth - margin - 60, yPos + 12)
  doc.text('Company Stamp : ___________________', pageWidth - margin - 60, yPos + 18)
  footer()
}

// Specifications
  const specifications = async()=>{

    for (let index = 0; index < invoice.specs.length; index++) {
      doc.addPage()
      doc.setTextColor(0,0,0)
      yPos = 20
      margin = 15
      doc.setFontSize(14)
      doc.setFont("helvetica", "bold")
      doc.text("Specifications Of ", pageWidth/2,yPos,{align:"center"})
      doc.setFontSize(10)
      doc.text(invoice.specs[index].product_name,pageWidth/2,yPos+5,{align:"center"})
      
      yPos +=5
      // 1ere partie

      // Overview
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin, yPos+5, pageWidth /2-20, 60,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin, yPos+13, 30, 52, 'FD')
      
      
      doc.text("Overview",  pageWidth /3-15, yPos + 10, { align: 'center' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Conditions", margin + 15, yPos + 20, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].vehicle_condition), margin+50,yPos + 20, { align: 'center' })
      divider(0.1,margin,yPos + 25,pageWidth /2-5,yPos + 25)
  
      doc.text("Mileage", margin + 15, yPos + 30, { align: 'center' })
      doc.text(String(invoice.specs[index].vehicle_mileage), margin+50,yPos + 30, { align: 'center' })
      divider(0.1,margin,yPos + 35,pageWidth /2-5,yPos + 35)
  
      doc.text("Production date", margin + 15, yPos + 40, { align: 'center' })
      doc.text(String(invoice.specs[index].production_date), margin + 50,yPos + 40, { align: 'center' })
      divider(0.1,margin,yPos + 45,pageWidth /2-5,yPos + 45)
  
      doc.text("Year", margin + 15, yPos + 50, { align: 'center' })
      doc.text(String(invoice.specs[index].vehicle_year), margin + 50, yPos + 50, { align: 'center' })
      divider(0.1,margin,yPos + 55,pageWidth /2-5,yPos + 55)
  
      doc.text("Country of Origin", margin + 15, yPos + 60, { align: 'center' })
      doc.text(invoice.specs[index].country_of_origin, margin + 50, yPos + 60, { align: 'center' })


      // Technical specification
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin+95, yPos+5, pageWidth /2-20, 60,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin+95, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin+95, yPos+13, 40, 52, 'FD')
      
      
      doc.text("Technical specification",  margin + 115, yPos + 10, { align: 'left' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Brand", margin + 115, yPos + 20, { align: 'center' })
      doc.text(invoice.specs[index].vehicle_brand, margin+150,yPos + 20, { align: 'center' })
      divider(0.1,margin+95,yPos + 25,pageWidth -margin,yPos + 25)
  
      doc.text("Model", margin + 115, yPos + 30, { align: 'center' })
      doc.text(invoice.specs[index].vehicle_model, margin+150,yPos + 30, { align: 'center' })
      divider(0.1,margin+95,yPos + 35,pageWidth -margin,yPos + 35)
  
      doc.text("Drive Type", margin + 115, yPos + 40, { align: 'center' })
      doc.text(String(invoice.specs[index].drive_type), margin + 150,yPos + 40, { align: 'center' })
      divider(0.1,margin+95,yPos + 45,pageWidth-margin,yPos + 45)
  
      doc.text("Wheelbases (mm)", margin + 115, yPos + 50, { align: 'center' })
      doc.text(String(invoice.specs[index].wheelbase), margin + 150, yPos + 50, { align: 'center' })
      divider(0.1,margin+95,yPos + 55,pageWidth -margin,yPos + 55)
  
      doc.text( "Economic speed /\nMaximum speed (km/h)", margin + 115, yPos + 60, { align: 'center' })
      doc.text(String(invoice.specs[index].speed), margin + 150, yPos + 60, { align: 'center' })

      yPos += 65
      // 2e partie 

      // Engine
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin, yPos+5, pageWidth /2-20, 70,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin, yPos+13, 30, 62, 'FD')
      
      
      doc.text("Engine",  pageWidth /3-15, yPos + 10, { align: 'center' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Number", margin + 15, yPos + 20, { align: 'center' })
      doc.text(String(invoice.specs[index].engine_number), margin+50,yPos + 20, { align: 'center' })
      divider(0.1,margin,yPos + 25,pageWidth /2-5,yPos + 25)
  
      doc.text("Brand", margin + 15, yPos + 30, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].engine_brand), margin+50,yPos + 30, { align: 'center' })
      divider(0.1,margin,yPos + 35,pageWidth /2-5,yPos + 35)
  
      doc.text("Model", margin + 15, yPos + 40, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].engine_model), margin + 50,yPos + 40, { align: 'center' })
      divider(0.1,margin,yPos + 45,pageWidth /2-5,yPos + 45)
  
      doc.text("Power", margin + 15, yPos + 50, { align: 'center' })
      doc.text(String(invoice.specs[index].power), margin + 50, yPos + 50, { align: 'center' })
      divider(0.1,margin,yPos + 55,pageWidth /2-5,yPos + 55)
  
      doc.text("Hor sepower", margin + 15, yPos + 60, { align: 'center' })
      doc.text(String(invoice.specs[index].hor_sepower), margin + 50, yPos + 60, { align: 'center' })
      divider(0.1,margin,yPos + 65,pageWidth /2-5,yPos + 65)

      doc.text("Emission standards", margin + 15, yPos + 70, { align: 'center' })
      doc.text(String(invoice.specs[index].engine_emissions), margin + 50, yPos + 70, { align: 'center' })


      // Dimensions(mm)
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin+95, yPos+5, pageWidth /2-20, 52,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin+95, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin+95, yPos+13, 40, 44, 'FD')
      
      
      doc.text("Dimensions(mm)",  margin + 115, yPos + 10, { align: 'left' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Chassis(mm)", margin + 115, yPos + 20, { align: 'center' })
      doc.text(String(invoice.specs[index].chassis_dimensions), margin+150,yPos + 20, { align: 'center' })
      divider(0.1,margin+95,yPos + 25,pageWidth -margin,yPos + 25)
  
      doc.text("Frame rear \nsuspension(mm)", margin + 115, yPos + 30, { align: 'center' })
      doc.text(String(invoice.specs[index].frame_rear_suspension), margin+150,yPos + 30, { align: 'center' })
      divider(0.1,margin+95,yPos + 35,pageWidth -margin,yPos + 35)
  
      doc.text("Overall Dimensions(mm)", margin + 115, yPos + 40, { align: 'center' })
      doc.text(String(invoice.specs[index].overall_dimensions), margin + 150,yPos + 40, { align: 'center' })
      divider(0.1,margin+95,yPos + 45,pageWidth-margin,yPos + 45)
  
      doc.text("Vehicle Dimensions(LxlxH)", margin + 115, yPos + 50, { align: 'center' })
      doc.text(String(invoice.specs[index].vehicle_dimensions), margin + 150, yPos + 50, { align: 'center' })
     
     
      yPos += 75
      // 3e partie 

      // Vehicle Weight
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin, yPos+5, pageWidth /2-20, 40,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin, yPos+13, 30, 32, 'FD')
      
      
      doc.text("Vehicle Weight",  pageWidth /3-15, yPos + 10, { align: 'center' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Curb Weight", margin + 15, yPos + 20, { align: 'center' })
      doc.text(String(invoice.specs[index].curb_weight), margin+50,yPos + 20, { align: 'center' })
      divider(0.1,margin,yPos + 25,pageWidth /2-5,yPos + 25)
  
      doc.text("Payload Capacity", margin + 15, yPos + 30, { align: 'center' })
      doc.text(String(invoice.specs[index].payload_capacity), margin+50,yPos + 30, { align: 'center' })
      divider(0.1,margin,yPos + 35,pageWidth /2-5,yPos + 35)
  
      doc.text("Gross Vehicle\n Weight (GVW)", margin + 15, yPos + 40, { align: 'center' })
      doc.text(String(invoice.specs[index].gvw), margin + 50,yPos + 40, { align: 'center' })
  


      // Gear box
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin+95, yPos+5, pageWidth /2-20, 40,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin+95, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin+95, yPos+13, 44, 32, 'FD')
      
      
      doc.text("Gear box",  margin + 115, yPos + 10, { align: 'left' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Brand", margin + 115, yPos + 20, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].gear_box_brand), margin+150,yPos + 20, { align: 'center' })
      divider(0.1,margin+95,yPos + 25,pageWidth -margin,yPos + 25)
  
      doc.text("Model", margin + 115, yPos + 30, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].gear_box_model), margin+150,yPos + 30, { align: 'center' })
      divider(0.1,margin+95,yPos + 35,pageWidth -margin,yPos + 35)
  
      doc.text("type", margin + 115, yPos + 40, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].transmission_type), margin + 150,yPos + 40, { align: 'center' })
  

      yPos += 45
      // 4e partie 

      // Suspension
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin, yPos+5, pageWidth /2-20, 40,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin, yPos+13, 30, 32, 'FD')
      
      
      doc.text("Suspension",  pageWidth /3-15, yPos + 10, { align: 'center' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Suspension Type", margin + 15, yPos + 20, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].suspension_type), margin+50,yPos + 20, { align: 'center' })
      divider(0.1,margin,yPos + 25,pageWidth /2-5,yPos + 25)
  
      doc.text("Front", margin + 15, yPos + 30, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].suspension_front), margin+50,yPos + 30, { align: 'center' })
      divider(0.1,margin,yPos + 35,pageWidth /2-5,yPos + 35)
  
      doc.text("Rear", margin + 15, yPos + 40, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].suspension_rear), margin + 50,yPos + 40, { align: 'center' })
  


      // Axles
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin+95, yPos+5, pageWidth /2-20, 50,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin+95, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin+95, yPos+13, 44, 42, 'FD')
      
      
      doc.text("Axles",  margin + 115, yPos + 10, { align: 'left' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Brand", margin + 115, yPos + 20, { align: 'center' })
      doc.text(invoice.specs[index].axle_brand, margin+150,yPos + 20, { align: 'center' })
      divider(0.1,margin+95,yPos + 25,pageWidth -margin,yPos + 25)
  
      doc.text("Front axle", margin + 115, yPos + 30, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].axle_front), margin+150,yPos + 30, { align: 'center' })
      divider(0.1,margin+95,yPos + 35,pageWidth -margin,yPos + 35)
      
      doc.text("Rear axle", margin + 115, yPos + 40, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].axle_rear), margin + 150,yPos + 40, { align: 'center' })
      divider(0.1,margin+95,yPos + 45,pageWidth -margin,yPos + 45)
      
      doc.text("Speed ratio", margin + 115, yPos + 50, { align: 'center' })
      doc.text(String(invoice.specs[index].axle_speed_ratio), margin + 150,yPos + 50, { align: 'center' })
  
      footer()
      // Nouvelle page pour la 5e partie
      doc.addPage()
      yPos=20

      // 5e partie
      // Fuel tank
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); 
      doc.roundedRect(margin, yPos+5, pageWidth /2-20, 30,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin, yPos+13, 30, 22, 'FD')
      
      
      doc.text("Fuel tank",  pageWidth /3-15, yPos + 10, { align: 'center' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Fuel Tank Capacity", margin + 15, yPos + 20, { align: 'center' })
      doc.text(String(invoice.specs[index].fuel_tank_capacity), margin+50,yPos + 20, { align: 'center' })
      divider(0.1,margin,yPos + 25,pageWidth /2-5,yPos + 25)
  
      doc.text("Fuel Type", margin + 15, yPos + 30, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].fuel_type), margin+50,yPos + 30, { align: 'center' })
  
      // Brake System
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin+95, yPos+5, pageWidth /2-20, 20,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin+95, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin+95, yPos+13, 44, 12, 'FD')
      
      
      doc.text("Brake System",  margin + 115, yPos + 10, { align: 'left' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Type", margin + 115, yPos + 20, { align: 'center' })
      doc.text(invoice.specs[index].brake_system, margin+150,yPos + 20, { align: 'center' })
      
      yPos += 30

       // 6e partie
      // Tires
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); 
      doc.roundedRect(margin, yPos+5, pageWidth /2-20, 20,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin, yPos+13, 30, 12, 'FD')
      
      
      doc.text("Tires",  pageWidth /3-15, yPos + 10, { align: 'center' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Type", margin + 15, yPos + 20, { align: 'center' })
      doc.text(invoice.specs[index].tire_size, margin+50,yPos + 20, { align: 'center' })
  
      // Cab
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin+95, yPos+5, pageWidth /2-20, 20,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin+95, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin+95, yPos+13, 44, 12, 'FD')
      
      
      doc.text("Cab",  margin + 115, yPos + 10, { align: 'left' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Type", margin + 115, yPos + 20, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].cabin_type), margin+150,yPos + 20, { align: 'center' })
      
      yPos += 30

       // 7e partie
      // Air filter
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); 
      doc.roundedRect(margin, yPos+5, pageWidth /2-20, 20,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin, yPos+13, 30, 12, 'FD')
      
      
      doc.text("Air filter",  pageWidth /3-15, yPos + 10, { align: 'center' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Type", margin + 15, yPos + 20, { align: 'center' })
      doc.text(capitalizeFirst(invoice.specs[index].air_filter), margin+50,yPos + 20, { align: 'center' })
  
      // Electrics
      //Cadre principal
      doc.setFillColor(255, 255, 255)
      doc.setDrawColor(235, 235, 235); // Orange clair
      doc.roundedRect(margin+95, yPos+5, pageWidth /2-20, 20,1,1, 'FD')
  
      doc.setFontSize(10)
      doc.setFont('helvetica', 'bold')
      doc.setTextColor(0,0,0)
  
      // Cadre entête
      doc.setFillColor(243, 244, 246) // Gris clair
      doc.rect(margin+95, yPos+5, pageWidth /2-20, 8, 'F')
  
      // Cadre vertical des titres
      doc.setFillColor(250, 250, 250)
      doc.rect(margin+95, yPos+13, 44, 12, 'FD')
      
      
      doc.text("Electrics",  margin + 115, yPos + 10, { align: 'left' })
      doc.setFontSize(8)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(107, 114, 128)
  
      doc.text("Type", margin + 115, yPos + 20, { align: 'center' })
      doc.text(invoice.specs[index].electrics, margin+150,yPos + 20, { align: 'center' })
  
      yPos += 50

      // URL de l'image à ajouter
      const imageUrl = invoice.specs[index].primary_image;
      // chargement de l'image
      const imageDataUrl = await imgUrlToDataUrl(imageUrl);
        
      doc.setFillColor(254, 121, 0)
      doc.setDrawColor(0, 0, 0); // Orange clair
      doc.roundedRect(margin+60, yPos, pageWidth /2-35, 70,1,1, 'FD')
      doc.addImage(imageDataUrl, 'PNG', margin+60, yPos, 70, 70);

          footer()
    }
    
  }
  
  //Generation du PDF
    header()
    subHeader()
    itemsTable()
    totalsSection()
    bankInfos()
    termsAndConditions()
    signature()
    await specifications()
  
  // Téléchargement
  doc.save(`Invoice_${invoice.number || 'XXXX'}.pdf`)
}