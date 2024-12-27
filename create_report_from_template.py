import comtypes.client
from docx import Document
import os

def replace_placeholders(doc_path, output_path, data):
    doc = Document(doc_path)

    # Replace placeholders in paragraphs
    for paragraph in doc.paragraphs:
        for key, value in data.items():
            if key in paragraph.text:
                for run in paragraph.runs:
                    run.text = run.text.replace(key, value)

    # Replace placeholders in text boxes (shapes)
    for shape in doc.inline_shapes:
        if shape.type == 3:  # Text box
            text_box = shape.text_frame
            if text_box is not None:
                for paragraph in text_box.paragraphs:
                    for key, value in data.items():
                        if key in paragraph.text:
                            for run in paragraph.runs:
                                run.text = run.text.replace(key, value)

    # Save the updated Word document
    doc.save(output_path)

def convert_to_pdf(word_path, pdf_path):
    word = comtypes.client.CreateObject("Word.Application")
    doc = word.Documents.Open(word_path)
    doc.SaveAs(pdf_path, FileFormat=17)  # 17 represents wdFormatPDF
    doc.Close()
    word.Quit()

if __name__ == '__main__':
    data = {
        '[Report Title]': 'Report 1',
        '[Your Name or Organization]': 'Euro Global Consultancy',
        '[Insert Date]': '27/12/2024',
        '[Your Email Address]': 'sadullah@mail.com',
        '[Your Phone Number]' : '1234567890',
        '[Your Website]': 'euroglobalconsultancy.com'
    }

    # Get absolute paths
    base_dir = os.path.dirname(os.path.abspath(__file__))
    template_path = os.path.join(base_dir, 'template.docx')
    word_output_path = os.path.join(base_dir, 'filled.docx')
    pdf_output_path = os.path.join(base_dir, 'filled.pdf')

    replace_placeholders(template_path, word_output_path, data)
    convert_to_pdf(word_output_path, pdf_output_path)

    print('Report generated as Word and PDF with exact formatting!')