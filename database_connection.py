import mysql.connector
from mysql.connector import Error
import PyPDF2
import re
import os

def connect_to_database(host, user, password, database):
    """
    Connect to a MySQL database.
    """
    try:
        connection = mysql.connector.connect(
            host=host,
            user=user,
            password=password,
            database=database
        )
        if connection.is_connected():
            print(f"Connected to MySQL database '{database}'")
            return connection
    except Error as e:
        print(f"Error connecting to MySQL: {e}")
        return None

def extract_values_from_pdf(pdf_file_path):
    """
    Extract values from a PDF file using regex.
    """
    extracted_data = {}
    try:
        with open(pdf_file_path, 'rb') as pdf_file:
            reader = PyPDF2.PdfReader(pdf_file)
            full_text = ""
            for page in reader.pages:
                full_text += page.extract_text()

            # Log extracted text for debugging
            print("Extracted Text from PDF:\n", full_text)

            # Use regex patterns to extract values
            extracted_data['Report Title'] = re.search(r"Report (\d+)", full_text).group(0)
            extracted_data['Prepared by'] = re.search(r"Prepared by:\s*(.*)", full_text).group(1)
            extracted_data['Date'] = re.search(r"Date:\s*(.*)", full_text).group(1)

            # Extract Email, Phone, and Website fields
            extracted_data['Email'] = re.search(r"Email:\s*(.*)", full_text).group(1)
            extracted_data['Phone'] = re.search(r"Phone:\s*(.*)", full_text).group(1)
            extracted_data['Website'] = re.search(r"Website:\s*(.*)", full_text).group(1)

    except AttributeError as e:
        print(f"Error extracting values from PDF: {e}. Check your regex patterns.")
    except Exception as e:
        print(f"Unexpected error extracting values from PDF: {e}")

    return extracted_data

def insert_data_into_database(connection, data):
    """
    Insert extracted data into the MySQL database.
    """
    try:
        cursor = connection.cursor()
        insert_query = """
        INSERT INTO reports (
            report_title,
            organization_name,
            report_date,
            email_address,
            phone_number,
            website
        ) VALUES (%s, %s, %s, %s, %s, %s);
        """
        # Map extracted data to database fields
        values = (
            data.get("Report Title", "N/A"),   # Map 'Report Title'
            data.get("Prepared by", None),    # Map 'Prepared by' to 'organization_name'
            data.get("Date", None),           # Map 'Date' to 'report_date'
            data.get("Email", None),          # Map 'Email' to 'email_address'
            data.get("Phone", None),          # Map 'Phone' to 'phone_number'
            data.get("Website", None)         # Map 'Website' to 'website'
        )
        
        # Log the mapped values for debugging
        print("Mapped Values for Insertion:", values)
        
        # Execute the query with mapped values
        cursor.execute(insert_query, values)
        connection.commit()
        print("Data inserted successfully into 'reports' table.")
    except Error as e:
        print(f"Error inserting data: {e}")
    finally:
        if cursor:
            cursor.close()

if __name__ == "__main__":
    # Configuration
    db_config = {
        "host": "localhost",
        "user": "root",
        "password": "",
        "database": "python_database"
    }

    # File path to the PDF
    pdf_file_path = "filled_v2.pdf"  # Replace with your file path

    # Step 1: Connect to the database
    connection = connect_to_database(**db_config)

    if connection:
        # Step 2: Extract values from the PDF
        extracted_data = extract_values_from_pdf(pdf_file_path)
        if extracted_data:
            print("Extracted Data from PDF:", extracted_data)

            # Step 3: Insert data into the database
            insert_data_into_database(connection, extracted_data)

        # Close the database connection
        connection.close()
        print("Database connection closed.")