/**
 * Contact Form API Endpoint for Vercel
 * Handles all contact form submissions (contact form, quick quote, newsletter)
 */

module.exports = async function handler(req, res) {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  // Handle preflight requests
  if (req.method === 'OPTIONS') {
    res.status(200).end();
    return;
  }

  // Only allow POST requests
  if (req.method !== 'POST') {
    res.status(405).json({ error: 'Method not allowed' });
    return;
  }

  try {
    const input = req.body;

    // Validate required fields
    if (!input || !input.name) {
      res.status(400).json({
        error: 'Missing required fields: name'
      });
      return;
    }

    // Sanitize input
    const name = String(input.name || '').trim();
    const email = String(input.email || '').trim();
    const subject = String(input.subject || 'Kontaktanfrage von Website').trim();
    const message = String(input.message || '').trim();
    const phone = String(input.phone || '').trim();
    const service = String(input.service || '').trim();
    const area = String(input.area || '').trim();

    // Validate email if provided
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (email && !emailRegex.test(email)) {
      res.status(400).json({ error: 'Invalid email address' });
      return;
    }

    // Use placeholder email if not provided
    const senderEmail = email || 'keine-email@meineallrounder.de';

    // Determine form type
    const formType = service ? 'Angebotsanfrage' : (input.subject === 'Newsletter Anmeldung' ? 'Newsletter Anmeldung' : 'Kontaktanfrage');
    const emailSubject = `${formType} von ${name}`;

    // Create email body
    let emailBody = `Neue ${formType} von der Website\n\n`;
    emailBody += `Name: ${name}\n`;
    emailBody += `E-Mail: ${senderEmail}\n`;

    if (phone) {
      emailBody += `Telefon: ${phone}\n`;
    }

    if (subject && subject !== 'Kontaktanfrage von Website' && subject !== 'Newsletter Anmeldung') {
      emailBody += `Betreff: ${subject}\n`;
    }

    if (service) {
      emailBody += `Art der Arbeit: ${service}\n`;
    }

    if (area) {
      emailBody += `Fläche: ${area} m²\n`;
    }

    if (message) {
      emailBody += `\nNachricht:\n${message}\n`;
    }

    emailBody += `\n---\n`;
    emailBody += `Gesendet am: ${new Date().toLocaleString('de-DE', { timeZone: 'Europe/Berlin' })}\n`;
    emailBody += `IP-Adresse: ${req.headers['x-forwarded-for'] || req.headers['x-real-ip'] || req.connection.remoteAddress || 'unbekannt'}\n`;

    // Recipient email
    const recipientEmail = 'info@meineallrounder.de';

    // Send email using Resend (free tier: 3,000 emails/month)
    // Get API key from: https://resend.com/api-keys
    // Then add RESEND_API_KEY to Vercel Environment Variables
    const RESEND_API_KEY = process.env.RESEND_API_KEY;
    
    if (RESEND_API_KEY) {
      // Use Resend API to send email
      try {
        const resendResponse = await fetch('https://api.resend.com/emails', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${RESEND_API_KEY}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            from: 'Meine Allrounder <noreply@meineallrounder.de>',
            to: [recipientEmail],
            reply_to: senderEmail,
            subject: emailSubject,
            text: emailBody
          })
        });

        if (!resendResponse.ok) {
          const errorData = await resendResponse.json();
          console.error('Resend API error:', errorData);
          throw new Error('Failed to send email via Resend');
        }
        
        console.log('Email sent successfully via Resend');
      } catch (emailError) {
        console.error('Email sending error:', emailError);
        // Log the submission even if email fails
        console.log('=== Contact Form Submission (Email Failed) ===');
        console.log('To:', recipientEmail);
        console.log('Subject:', emailSubject);
        console.log('Body:', emailBody);
        console.log('==============================');
      }
    } else {
      // Log the submission if no email service is configured
      // You can check Vercel logs to see all submissions
      console.log('=== Contact Form Submission ===');
      console.log('To:', recipientEmail);
      console.log('Subject:', emailSubject);
      console.log('Body:', emailBody);
      console.log('');
      console.log('⚠️  RESEND_API_KEY not set. Email not sent.');
      console.log('📧 To enable email sending:');
      console.log('   1. Sign up at https://resend.com (free)');
      console.log('   2. Create API key at https://resend.com/api-keys');
      console.log('   3. Add RESEND_API_KEY to Vercel Environment Variables');
      console.log('   4. Redeploy the project');
      console.log('==============================');
    }

    // Return success response
    res.status(200).json({
      success: true,
      message: formType === 'Newsletter Anmeldung' 
        ? 'Vielen Dank für Ihr Abonnement! Sie erhalten ab sofort unsere Neuigkeiten.'
        : formType === 'Angebotsanfrage'
        ? 'Vielen Dank für Ihre Anfrage! Wir werden Ihnen innerhalb von 24 Stunden ein unverbindliches Angebot zusenden.'
        : 'Ihre Nachricht wurde erfolgreich gesendet. Wir werden uns bald bei Ihnen melden.'
    });

  } catch (error) {
    console.error('Contact form error:', error);
    res.status(500).json({
      error: 'Failed to process form submission',
      message: 'Es gab einen Fehler beim Senden Ihrer Nachricht. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns direkt unter info@meineallrounder.de'
    });
  }
};
