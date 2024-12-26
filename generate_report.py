from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas
import os

def generate_pdf_report(template_path, output_path, context):
    """
    Generates a PDF report based on a template.

    Args:
        template_path (str): Path to the template file.
        output_path (str): Path where the generated PDF report will be saved.
        context (dict): A dictionary containing placeholder keys and their corresponding replacement values.
    """
    # Ensure the template file exists
    if not os.path.exists(template_path):
        raise FileNotFoundError(f"Template file '{template_path}' does not exist.")
    
    # Read the template content
    with open(template_path, 'r') as template_file:
        template_content = template_file.read()
    
    # Replace placeholders in the template with values from the context
    report_content = template_content
    for placeholder, value in context.items():
        report_content = report_content.replace(f"{{{{{placeholder}}}}}", str(value))
    
    # Create a PDF using reportlab
    pdf = canvas.Canvas(output_path, pagesize=letter)
    pdf.setFont("Helvetica", 12)
    width, height = letter

    # Write the content to the PDF, line by line
    y_position = height - 40  # Start from the top margin
    for line in report_content.splitlines():
        pdf.drawString(40, y_position, line)
        y_position -= 20  # Move to the next line

    # Save the PDF
    pdf.save()

    print(f"PDF report generated successfully at: {output_path}")

# Example usage
if __name__ == "__main__":
    template_file = "report_template.txt"  # The template file with placeholders
    output_file = "generated_report.pdf"  # The file where the PDF report will be saved
    data_context = {
        "name": "Sadullah",
        "age": 24,
        "location": "Nagercoil",
        "date": "2024-12-25"
    }

    generate_pdf_report(template_file, output_file, data_context)