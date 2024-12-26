import os
from PyPDF2 import PdfReader
from generate_report import generate_pdf_report

def read_pdf_content(pdf_path):
    """
    Reads and extracts text from a PDF file.

    Args:
        pdf_path (str): Path to the PDF file.

    Returns:
        str: The extracted text content.
    """
    if not os.path.exists(pdf_path):
        raise FileNotFoundError(f"PDF file '{pdf_path}' does not exist.")
    
    # Open the PDF and extract its text
    reader = PdfReader(pdf_path)
    content = ""
    for page in reader.pages:
        content += page.extract_text()
    return content

if __name__ == "__main__":
    # Define file paths and context
    template_file = "report_template.txt"
    output_file = "generated_report.pdf"
    data_context = {
        "name": "Sadullah",
        "age": 24,
        "location": "Nagercoil",
        "date": "2024-12-25"
    }

    # Generate the PDF report using the first script
    generate_pdf_report(template_file, output_file, data_context)

    # Read and print the content of the generated PDF
    pdf_content = read_pdf_content(output_file)
    print("Extracted PDF Content:")
    print(pdf_content)
