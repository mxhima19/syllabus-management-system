from flask import Flask, render_template, request, redirect, url_for, flash
from flask_login import LoginManager, login_user, logout_user, login_required, current_user
from werkzeug.security import generate_password_hash, check_password_hash
from models import db, User, Course, Syllabus, Enrollment
import os

app = Flask(__name__)
app.config['SECRET_KEY'] = os.urandom(24)
app.config['SQLALCHEMY_DATABASE_URI'] = 'sqlite:///syllabus.db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
app.config['DEBUG'] = True  # Enable debug mode

db.init_app(app)
login_manager = LoginManager()
login_manager.init_app(app)
login_manager.login_view = 'login'

@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))

# Authentication routes
@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form.get('username')
        password = request.form.get('password')
        user = User.query.filter_by(username=username).first()
        
        if user and check_password_hash(user.password_hash, password):
            login_user(user)
            return redirect(url_for('dashboard'))
        flash('Invalid username or password')
    return render_template('login.html')

@app.route('/logout')
@login_required
def logout():
    logout_user()
    return redirect(url_for('login'))

# Dashboard routes
@app.route('/')
@login_required
def dashboard():
    if current_user.role == 'admin':
        return render_template('admin_dashboard.html')
    elif current_user.role == 'staff':
        return render_template('staff_dashboard.html')
    else:
        return render_template('student_dashboard.html')

# Course management routes
@app.route('/courses')
@login_required
def courses():
    if current_user.role in ['admin', 'staff']:
        courses = Course.query.all()
    else:
        enrollments = Enrollment.query.filter_by(student_id=current_user.id).all()
        course_ids = [e.course_id for e in enrollments]
        courses = Course.query.filter(Course.id.in_(course_ids)).all()
    return render_template('courses.html', courses=courses)

@app.route('/course/<int:course_id>/syllabus')
@login_required
def view_syllabus(course_id):
    syllabus = Syllabus.query.filter_by(course_id=course_id, is_active=True).first()
    if not syllabus:
        flash('No active syllabus found for this course')
        return redirect(url_for('courses'))
    return render_template('syllabus.html', syllabus=syllabus)

# Admin routes
@app.route('/admin/users')
@login_required
def manage_users():
    if current_user.role != 'admin':
        flash('Unauthorized access')
        return redirect(url_for('dashboard'))
    users = User.query.all()
    return render_template('users.html', users=users)

# Staff routes
@app.route('/staff/syllabus/create', methods=['GET', 'POST'])
@login_required
def create_syllabus():
    if current_user.role != 'staff':
        flash('Access denied.', 'danger')
        return redirect(url_for('dashboard'))
    
    if request.method == 'POST':
        course_id = request.form.get('course_id')
        academic_year = request.form.get('academic_year')
        semester = request.form.get('semester')
        
        syllabus = Syllabus(
            course_id=course_id,
            staff_id=current_user.id,
            academic_year=academic_year,
            semester=semester,
            objectives=request.form.get('objectives'),
            prerequisites=request.form.get('prerequisites'),
            textbooks=request.form.get('textbooks'),
            topics=request.form.get('topics'),
            grading_policy=request.form.get('grading_policy')
        )
        
        db.session.add(syllabus)
        db.session.commit()
        flash('Syllabus created successfully')
        return redirect(url_for('courses'))
    
    courses = Course.query.all()
    return render_template('create_syllabus.html', courses=courses)

# Error handlers
@app.errorhandler(404)
def page_not_found(e):
    return render_template('404.html'), 404

@app.errorhandler(500)
def internal_server_error(e):
    return render_template('500.html'), 500

if __name__ == '__main__':
    with app.app_context():
        db.create_all()
        print("Database tables created successfully!")
    app.run(debug=True) 