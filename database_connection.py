import mysql.connector
from mysql.connector import Error
from docx import Document
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

def extract_values_from_docx(docx_path):
    """
    Extract key-value pairs from a filled Word (.docx) file.
    """
    data = {}
    try:
        doc = Document(docx_path)
        for paragraph in doc.paragraphs:
            text = paragraph.text.strip()
            if ":" in text:  # Check for key-value pairs
                key, value = text.split(":", 1)
                key = key.strip()  # Clean up key
                value = value.strip()  # Clean up value
                data[key] = value
        return data
    except Exception as e:
        print(f"Error reading docx file: {e}")
        return None

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
            "N/A",                            # Placeholder for 'Report Title'
            data.get("Prepared by", None),    # Maps to 'organization_name'
            data.get("Date", None),           # Maps to 'report_date'
            data.get("Email", "").replace("\n", " ").split("Phone:")[0].strip(),  # Extract email only
            data.get("Email", "").split("Phone:")[1].split("\nWebsite:")[0].strip() if "Phone:" in data.get("Email", "") else None,  # Extract phone number
            data.get("Email", "").split("Website:")[1].strip() if "Website:" in data.get("Email", "") else None  # Extract website
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

    # File path
    base_dir = os.path.dirname(os.path.abspath(__file__))
    docx_file_path = os.path.join(base_dir, "filled_v2.docx")  # Replace with your filled report filename

    # Step 1: Connect to the database
    connection = connect_to_database(**db_config)

    if connection:
        # Step 2: Extract values from the Word document
        extracted_data = extract_values_from_docx(docx_file_path)
        if extracted_data:
            print("Extracted Data:", extracted_data)

            # Step 3: Insert data into the database
            insert_data_into_database(connection, extracted_data)

        # Close the database connection
        connection.close()
        print("Database connection closed.")