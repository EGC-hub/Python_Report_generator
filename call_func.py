from generate_report import generate_pdf_report
import PyPDF2
import re

def extract_values_from_pdf(pdf_file_path):
    """
    Extract values from a PDF file.
    """
    extracted_data = {}
    try:
        with open(pdf_file_path, 'rb') as pdf_file:
            reader = PyPDF2.PdfReader(pdf_file)
            full_text = ""
            for page in reader.pages:
                full_text += page.extract_text()

            # Log extracted text for debugging
            print("Extracted Text from PDF:", full_text)

            # Use regex or specific patterns to extract values
            extracted_data['name'] = re.search(r"Hello,\s*(.*?)!", full_text).group(1)
            extracted_data['age'] = re.search(r"You are\s*(\d+)\s*years old", full_text).group(1)
            extracted_data['location'] = re.search(r"live in\s*(.*)\.", full_text).group(1)
            extracted_data['date'] = re.search(r"Date:\s*(.*)", full_text).group(1)

    except AttributeError as e:
        print(f"Error extracting values from PDF: {e}. Check your regex patterns.")
    except Exception as e:
        print(f"Unexpected error extracting values from PDF: {e}")

    return extracted_data


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

    # File path to the generated PDF
    generated_pdf = "generated_report.pdf"

    # Extract values from the PDF
    extracted_data = extract_values_from_pdf(generated_pdf)

    # Print extracted data
    print("Extracted Data from PDF:", extracted_data)