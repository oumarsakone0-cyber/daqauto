import jsPDF from 'jspdf'
import { imgUrlToDataUrl } from './imgUrlToDataUrl.js'
import logo from "../assets/favicon.jpg"
import { formatCurrency, formatDate, capitalizeFirst } from './utils.js'

const generateContractIdWithTimestamp = () => {
  const timestamp = Math.floor(Date.now() / 1000)
  const random = Math.random().toString(36).substring(2, 8).toUpperCase()
  return `CON-${timestamp}-${random}`
}

export const downloadContract = async(contract,subtotal,total) => {

   
    contract.number = generateContractIdWithTimestamp() // ou une autre fonction

  const writeWrapped = (text, x = margin, lineHeight = 5) => {
    const maxWidth = pageWidth - margin - 15;
    const lines = doc.splitTextToSize(text, maxWidth);
    if (yPos > pageHeight - 40) {
      footer();
      doc.addPage();
      yPos = 20;
    }
    doc.text(lines, x, yPos + 5);
    yPos += lines.length * lineHeight ;
};

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
    doc.text('Sales Contract generated on DaqAuto.com ', pageWidth / 2, pageHeight - 10, { align: 'center' })
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
  doc.text('SALES CONTRACT', margin+10, yPos)
  
  doc.setFontSize(7)
  doc.setTextColor(107, 114, 128)
  doc.setFont('helvetica', 'normal')
  doc.text('DAQ AUTO Marketplace',margin+10, yPos+4)
  

  // Numéro de facture
  margin = 20
  doc.setFillColor(254, 121, 0)
  doc.setDrawColor(254, 121, 0);
  doc.roundedRect(pageWidth - margin -30, yPos-4, 40, 6,1,1, 'FD')
  
  doc.setTextColor(255, 255, 255)
  doc.setFont('helvetica', 'bold')
  doc.setFontSize(9)
  doc.text(contract.number || 'cont-XXXX-XXX', pageWidth - margin-10, yPos, { align: 'center' })

  // Dates (sous le n°)
  doc.setTextColor(0, 0, 0)
  doc.setFontSize(8)
  doc.setFont('helvetica', 'bold')
  doc.text(`Date: ${formatDate(contract.date)}`, pageWidth - margin-7, yPos+7, { align: 'right' })

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
  doc.text(contract.client.name || 'Customer name', pageWidth - margin, yPos+4,{ align: 'left' })
  doc.setFontSize(7)
  doc.setTextColor(107, 114, 128)
  doc.text(contract.client.address || 'Customer address', pageWidth - margin, yPos + 7,{ align: 'left' })
  doc.text(contract.client.phone || '+225 XX XX XX XX XX', pageWidth - margin, yPos + 10,{ align: 'left' })
  doc.text(contract.client.email || 'email@exemple.com', pageWidth - margin, yPos + 13,{ align: 'left' })
}
  
// Section des articles
const itemsTable =()=>{
  // Tableau des articles
  yPos += 30
  margin = 15
doc.setFontSize(8)
  doc.setTextColor(107, 114, 128)
  doc.setFont('helvetica', 'normal')
  doc.text('The sellers agrees to sell and the buyer agrees to buy the undermentioned goods on the terms and conditions stated below :',margin, yPos-3)

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
  for (let index = 0; index < contract.items.length; index++) {
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }

    doc.text(String(index+1),margin + 5, yPos , { align: 'center' })
    doc.text( contract.items[index].product_type || "N/A",margin + 15, yPos, { align: 'center' })
    doc.text(contract.items[index].product_name || "N/A",margin + 50, yPos, { align: 'center' })
    doc.text(contract.items[index].trim_number || "N/A",margin + 90, yPos, { align: 'center' })
    doc.text(contract.items[index].vin || "N/A",margin + 110, yPos, { align: 'center' } )
    doc.text(contract.items[index].stock_number || "N/A",margin + 130, yPos, { align: 'center' } )
    doc.text(contract.items[index].color || "N/A", margin + 145, yPos, { align: 'center' })
    doc.text(String(contract.items[index].quantity), margin + 152, yPos, { align: 'center' })
    doc.text(formatCurrency(contract.items[index].price), margin +163, yPos, { align: 'center' })
    doc.text(formatCurrency(contract.items[index].quantity * contract.items[index].price), margin + 175, yPos, { align: 'center' })
    
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
    doc.setFontSize(8)
    doc.setFont('helvetica', 'bold')
    doc.text('1.1 Terms of Payment :  ', margin, yPos)
    doc.text('1.2 Period of the refund of the advance payment :  ', margin, yPos+5)
    doc.text('1.3 The payment currency under the agreement is :  ', margin, yPos+10)

    doc.setFont('helvetica', 'normal')
    doc.text('100% T/T in advance', margin+35, yPos)
    doc.text('180 (One hundred eighty) calendar days from the date of non-delivery of the goods.', margin+70, yPos+5)
    doc.text('USD', margin+70, yPos+10)


    
    yPos += 20
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
    doc.setTextColor(107, 114, 128)
    doc.setFont('helvetica', 'bold')
    doc.text('2.1 Transport Route :', margin, yPos)
    doc.text('2.2 Mode of shipment :', margin, yPos+5)
    doc.text('2.3 Time of shipment :', margin, yPos+10)
    doc.text('2.4 Partial shipment :', margin, yPos+15)
    doc.text('2.5 Delivery time:', margin, yPos+20)

    doc.setFont('helvetica', 'normal')
    doc.text('XXX, XXXX  From Chongqing,via XXX China, to XXXX', margin+35, yPos)
    doc.text('BY SEA', margin+35, yPos+5)
    doc.text('DURING 30 CALENDAR DAYS FROM THE DATE OF THE SELLER RECEIVES THE FULL PAYMENT.', margin+35, yPos+10)
    doc.text('NOT ALLOWED', margin+35, yPos+15)
    doc.text('during 90 (ninety) calendar days from the date of shipment of cars.', margin+35, yPos+20)

    yPos += 30
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
    doc.setTextColor(107, 114, 128)
    doc.setFont('helvetica', 'bold')
    doc.text('3. Transportation Insurance : ', margin, yPos-2)
    doc.setFont('helvetica', 'normal')
    doc.text('The seller is responsible for purchasing  transport insurance.', margin, yPos+5)
    
    yPos += 15
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
    doc.setTextColor(107, 114, 128)
    doc.setFont('helvetica', 'bold')
    doc.text('4. Quality of Products : ', margin, yPos-2)
    yPos=yPos+5
    doc.text('A. ', margin, yPos)
    doc.setFont('helvetica', 'normal')
    writeWrapped('The seller shall ensure that the quality of the delivered goods is qualified. After the goods arrive at the destination station, if the buyer discovers that the quality/quantity/weight of the goods do not comply with the contract provisions, in addition to being the responsibility of the insurance company/or the carrier, the buyer may make a claim against the seller based on the inspection certificate issued by the mutually agreed inspection agency. Claims for quality discrepancies must be made within 7 days from the date of arrival of the goods at the destination station, and claims for quantity/weight discrepancies must be made within 3 days from the date of arrival of the goods at the destination station. The seller shall reply to the buyer within 7 days of receiving the claim notice. After the goods arrive at the destination port, the buyer is obliged to clean up the goods in a timely manner and take appropriate protective and maintenance measures (such as rain protection, battery inspection, timely charging, etc.)')
    doc.setFont('helvetica', 'bold')
    doc.text('B. ', margin, yPos)
    doc.setFont('helvetica', 'normal')
    writeWrapped('The Seller shall not be deemed to breach the contract for failing to clear the customs, be punished or even confiscated due to the homologation, customs clearance, laws and market. The Buyer still undertakes all the obligations including payment on time.')

  yPos += 10
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
    doc.setTextColor(107, 114, 128)
    doc.setFont('helvetica', 'bold')
    doc.text('5. Force Majeure : ', margin, yPos-2)
    doc.setFont('helvetica', 'normal')
    writeWrapped('The Seller shall not be held responsible for the delay in shipment or non-delivery of the goods due to Force Majeure causes. However, in such a case, the Seller shall inform the Buyer for the occurrence mentioned above in written form and if it is requested by the Buyer, shall also supply the Buyer with the documents attesting the existence of such a cause or causes.')
    
  
  yPos += 10
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
    doc.setTextColor(107, 114, 128)
    doc.setFont('helvetica', 'bold')
    doc.text('6. Validity : ', margin, yPos-2)
    yPos = yPos + 5
    doc.text('A. ', margin, yPos)
    doc.setFont('helvetica', 'normal')
    writeWrapped('This contract comes into effect from the signing or stamp date.')
    doc.setFont('helvetica', 'bold')
    yPos = yPos +5
    doc.text('B. ', margin, yPos)
    doc.setFont('helvetica', 'normal')
    writeWrapped('Since this contract is signed by both parties, Buyer guarantees to pay in time unconditionally . Buyer should not refuse to pay the goods by any reason of quality and market problems. Otherwise, Buyer will be regarded as the breach of agreement for payment and will bear all the losses related to such agreement.')
  
  
    yPos += 10
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
    doc.setTextColor(107, 114, 128)
    doc.setFont('helvetica', 'bold')
    doc.text('7. Arbitration : ', margin, yPos-2)
    doc.setFont('helvetica', 'normal')
    writeWrapped('All disputes arising from the performance of this Contract shall be settled through friendly negotiation. In case no settlement can be reached, the dispute shall then be submitted to China International Economic and Trade Arbitration Commission Southwest Sub-Commission for arbitration in accordance with its rules in effect at the time of applying for arbitration, during which Chinese laws shall governs. The award of the arbitration shall be final and binding upon both parties. The official languages of arbitration shall be English.')

  yPos += 5
     if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
    doc.setTextColor(107, 114, 128)
    doc.setFont('helvetica', 'bold')
    doc.text('8.1 Transport Route :', margin, yPos)
    doc.setFont('helvetica', 'normal')
    writeWrapped('This contract is made in English in two originals, each party shall hold one copy.  Signed and stamped Scan and the original copy enjoy the equal value')
    yPos= yPos + 5
    doc.setFont('helvetica', 'bold')
    doc.text('8.2 Mode of shipment :', margin, yPos)
    doc.setFont('helvetica', 'normal')
    writeWrapped('shall be valid until December 31, 2025. If either party fails to notify the termination of the agreement in advance before the expiration date, this agreement shall be automatically extended to the next calendar year.')


  yPos += 10
   if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
  // Notes
  if (contract.notes) {
    doc.setTextColor(107, 114, 128)
    doc.setFont('helvetica', 'bold')
    doc.text('NOTES / CONDITIONS', margin, yPos)
    doc.setFont('helvetica', 'normal')
    
    
    const splitNotes = doc.splitTextToSize(contract.notes, pageWidth - 40)
    doc.text(splitNotes, margin, yPos + 5)
    yPos += splitNotes.length * 5 + 10
  }
}

// signature
const signature = ()=>{
  yPos += 10
  if (yPos > pageHeight - 40) {
      footer()
      doc.addPage()
      yPos = 20
    }
  doc.setTextColor(107, 114, 128)
  doc.setFont('helvetica', 'normal')
  doc.text('Signature of the Buyer : ___________________',  margin , yPos + 12)
  doc.text('Signature of the Seller : ___________________', pageWidth - margin - 60, yPos + 12)
  footer()
}

// Specifications
  const specifications = async()=>{

    for (let index = 0; index < contract.specs.length; index++) {
      doc.addPage()
      doc.setTextColor(0,0,0)
      yPos = 20
      margin = 15
      doc.setFontSize(14)
      doc.setFont("helvetica", "bold")
      doc.text("Specifications Of ", pageWidth/2,yPos,{align:"center"})
      doc.setFontSize(10)
      doc.text(contract.specs[index].product_name,pageWidth/2,yPos+5,{align:"center"})
      
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
      doc.text(capitalizeFirst(contract.specs[index].vehicle_condition), margin+50,yPos + 20, { align: 'center' })
      divider(0.1,margin,yPos + 25,pageWidth /2-5,yPos + 25)
  
      doc.text("Mileage", margin + 15, yPos + 30, { align: 'center' })
      doc.text(String(contract.specs[index].vehicle_mileage), margin+50,yPos + 30, { align: 'center' })
      divider(0.1,margin,yPos + 35,pageWidth /2-5,yPos + 35)
  
      doc.text("Production date", margin + 15, yPos + 40, { align: 'center' })
      doc.text(String(contract.specs[index].production_date), margin + 50,yPos + 40, { align: 'center' })
      divider(0.1,margin,yPos + 45,pageWidth /2-5,yPos + 45)
  
      doc.text("Year", margin + 15, yPos + 50, { align: 'center' })
      doc.text(String(contract.specs[index].vehicle_year), margin + 50, yPos + 50, { align: 'center' })
      divider(0.1,margin,yPos + 55,pageWidth /2-5,yPos + 55)
  
      doc.text("Country of Origin", margin + 15, yPos + 60, { align: 'center' })
      doc.text(contract.specs[index].country_of_origin, margin + 50, yPos + 60, { align: 'center' })


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
      doc.text(contract.specs[index].vehicle_brand, margin+150,yPos + 20, { align: 'center' })
      divider(0.1,margin+95,yPos + 25,pageWidth -margin,yPos + 25)
  
      doc.text("Model", margin + 115, yPos + 30, { align: 'center' })
      doc.text(contract.specs[index].vehicle_model, margin+150,yPos + 30, { align: 'center' })
      divider(0.1,margin+95,yPos + 35,pageWidth -margin,yPos + 35)
  
      doc.text("Drive Type", margin + 115, yPos + 40, { align: 'center' })
      doc.text(String(contract.specs[index].drive_type), margin + 150,yPos + 40, { align: 'center' })
      divider(0.1,margin+95,yPos + 45,pageWidth-margin,yPos + 45)
  
      doc.text("Wheelbases (mm)", margin + 115, yPos + 50, { align: 'center' })
      doc.text(String(contract.specs[index].wheelbase), margin + 150, yPos + 50, { align: 'center' })
      divider(0.1,margin+95,yPos + 55,pageWidth -margin,yPos + 55)
  
      doc.text( "Economic speed /\nMaximum speed (km/h)", margin + 115, yPos + 60, { align: 'center' })
      doc.text(String(contract.specs[index].speed), margin + 150, yPos + 60, { align: 'center' })

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
      doc.text(String(contract.specs[index].engine_number), margin+50,yPos + 20, { align: 'center' })
      divider(0.1,margin,yPos + 25,pageWidth /2-5,yPos + 25)
  
      doc.text("Brand", margin + 15, yPos + 30, { align: 'center' })
      doc.text(capitalizeFirst(contract.specs[index].engine_brand), margin+50,yPos + 30, { align: 'center' })
      divider(0.1,margin,yPos + 35,pageWidth /2-5,yPos + 35)
  
      doc.text("Model", margin + 15, yPos + 40, { align: 'center' })
      doc.text(capitalizeFirst(contract.specs[index].engine_model), margin + 50,yPos + 40, { align: 'center' })
      divider(0.1,margin,yPos + 45,pageWidth /2-5,yPos + 45)
  
      doc.text("Power", margin + 15, yPos + 50, { align: 'center' })
      doc.text(String(contract.specs[index].power), margin + 50, yPos + 50, { align: 'center' })
      divider(0.1,margin,yPos + 55,pageWidth /2-5,yPos + 55)
  
      doc.text("Hor sepower", margin + 15, yPos + 60, { align: 'center' })
      doc.text(String(contract.specs[index].hor_sepower), margin + 50, yPos + 60, { align: 'center' })
      divider(0.1,margin,yPos + 65,pageWidth /2-5,yPos + 65)

      doc.text("Emission standards", margin + 15, yPos + 70, { align: 'center' })
      doc.text(String(contract.specs[index].engine_emissions), margin + 50, yPos + 70, { align: 'center' })


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
      doc.text(String(contract.specs[index].chassis_dimensions), margin+150,yPos + 20, { align: 'center' })
      divider(0.1,margin+95,yPos + 25,pageWidth -margin,yPos + 25)
  
      doc.text("Frame rear \nsuspension(mm)", margin + 115, yPos + 30, { align: 'center' })
      doc.text(String(contract.specs[index].frame_rear_suspension), margin+150,yPos + 30, { align: 'center' })
      divider(0.1,margin+95,yPos + 35,pageWidth -margin,yPos + 35)
  
      doc.text("Overall Dimensions(mm)", margin + 115, yPos + 40, { align: 'center' })
      doc.text(String(contract.specs[index].overall_dimensions), margin + 150,yPos + 40, { align: 'center' })
      divider(0.1,margin+95,yPos + 45,pageWidth-margin,yPos + 45)
  
      doc.text("Vehicle Dimensions(LxlxH)", margin + 115, yPos + 50, { align: 'center' })
      doc.text(String(contract.specs[index].vehicle_dimensions), margin + 150, yPos + 50, { align: 'center' })
     
     
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
      doc.text(String(contract.specs[index].curb_weight), margin+50,yPos + 20, { align: 'center' })
      divider(0.1,margin,yPos + 25,pageWidth /2-5,yPos + 25)
  
      doc.text("Payload Capacity", margin + 15, yPos + 30, { align: 'center' })
      doc.text(String(contract.specs[index].payload_capacity), margin+50,yPos + 30, { align: 'center' })
      divider(0.1,margin,yPos + 35,pageWidth /2-5,yPos + 35)
  
      doc.text("Gross Vehicle\n Weight (GVW)", margin + 15, yPos + 40, { align: 'center' })
      doc.text(String(contract.specs[index].gvw), margin + 50,yPos + 40, { align: 'center' })
  


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
      doc.text(capitalizeFirst(contract.specs[index].gear_box_brand), margin+150,yPos + 20, { align: 'center' })
      divider(0.1,margin+95,yPos + 25,pageWidth -margin,yPos + 25)
  
      doc.text("Model", margin + 115, yPos + 30, { align: 'center' })
      doc.text(capitalizeFirst(contract.specs[index].gear_box_model), margin+150,yPos + 30, { align: 'center' })
      divider(0.1,margin+95,yPos + 35,pageWidth -margin,yPos + 35)
  
      doc.text("type", margin + 115, yPos + 40, { align: 'center' })
      doc.text(capitalizeFirst(contract.specs[index].transmission_type), margin + 150,yPos + 40, { align: 'center' })
  

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
      doc.text(capitalizeFirst(contract.specs[index].suspension_type), margin+50,yPos + 20, { align: 'center' })
      divider(0.1,margin,yPos + 25,pageWidth /2-5,yPos + 25)
  
      doc.text("Front", margin + 15, yPos + 30, { align: 'center' })
      doc.text(capitalizeFirst(contract.specs[index].suspension_front), margin+50,yPos + 30, { align: 'center' })
      divider(0.1,margin,yPos + 35,pageWidth /2-5,yPos + 35)
  
      doc.text("Rear", margin + 15, yPos + 40, { align: 'center' })
      doc.text(capitalizeFirst(contract.specs[index].suspension_rear), margin + 50,yPos + 40, { align: 'center' })
  


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
      doc.text(contract.specs[index].axle_brand, margin+150,yPos + 20, { align: 'center' })
      divider(0.1,margin+95,yPos + 25,pageWidth -margin,yPos + 25)
  
      doc.text("Front axle", margin + 115, yPos + 30, { align: 'center' })
      doc.text(capitalizeFirst(contract.specs[index].axle_front), margin+150,yPos + 30, { align: 'center' })
      divider(0.1,margin+95,yPos + 35,pageWidth -margin,yPos + 35)
      
      doc.text("Rear axle", margin + 115, yPos + 40, { align: 'center' })
      doc.text(capitalizeFirst(contract.specs[index].axle_rear), margin + 150,yPos + 40, { align: 'center' })
      divider(0.1,margin+95,yPos + 45,pageWidth -margin,yPos + 45)
      
      doc.text("Speed ratio", margin + 115, yPos + 50, { align: 'center' })
      doc.text(String(contract.specs[index].axle_speed_ratio), margin + 150,yPos + 50, { align: 'center' })
  
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
      doc.text(String(contract.specs[index].fuel_tank_capacity), margin+50,yPos + 20, { align: 'center' })
      divider(0.1,margin,yPos + 25,pageWidth /2-5,yPos + 25)
  
      doc.text("Fuel Type", margin + 15, yPos + 30, { align: 'center' })
      doc.text(capitalizeFirst(contract.specs[index].fuel_type), margin+50,yPos + 30, { align: 'center' })
  
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
      doc.text(contract.specs[index].brake_system, margin+150,yPos + 20, { align: 'center' })
      
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
      doc.text(contract.specs[index].tire_size, margin+50,yPos + 20, { align: 'center' })
  
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
      doc.text(capitalizeFirst(contract.specs[index].cabin_type), margin+150,yPos + 20, { align: 'center' })
      
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
      doc.text(capitalizeFirst(contract.specs[index].air_filter), margin+50,yPos + 20, { align: 'center' })
  
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
      doc.text(contract.specs[index].electrics, margin+150,yPos + 20, { align: 'center' })
  
      yPos += 50

      // URL de l'image à ajouter
      const imageUrl = contract.specs[index].primary_image;
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
  doc.save(`contract_${contract.number || 'XXXX'}.pdf`)
}