from app import app, db
from models import User
from werkzeug.security import generate_password_hash

def create_staff_user():
    with app.app_context():
        # Check if staff user already exists
        staff = User.query.filter_by(username='staff').first()
        if not staff:
            staff = User(
                username='staff',
                email='staff@bmsit.in',
                password_hash=generate_password_hash('staff123'),
                role='staff',
                department='Computer Science'
            )
            db.session.add(staff)
            db.session.commit()
            print("Staff user created successfully!")
            print("Username: staff")
            print("Password: staff123")
        else:
            print("Staff user already exists!")

if __name__ == '__main__':
    create_staff_user() 