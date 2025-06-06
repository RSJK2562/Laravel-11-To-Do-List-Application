# Laravel 11 To-Do List Application

A modern, responsive to-do list application built with Laravel 11, featuring AJAX functionality, real-time updates, and a beautiful user interface.

## ✨ Features

- ✅ **Add Tasks**: Press Enter or click Add button (no page reload)
- ✅ **Mark Complete**: Click checkbox to mark tasks as completed (tasks disappear from active view)
- ✅ **Show All Tasks**: Toggle between active tasks and all tasks (completed + active)
- ✅ **Delete Tasks**: Delete tasks with confirmation dialog
- ✅ **Duplicate Prevention**: No duplicate tasks allowed (case-insensitive)
- ✅ **Responsive Design**: Works perfectly on desktop and mobile
- ✅ **Real-time Updates**: AJAX-powered interface with smooth animations
- ✅ **Modern UI**: Bootstrap 5 with custom CSS and Font Awesome icons

## 🚀 Quick Start

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js (optional, for asset compilation)

### Installation

1. **Clone the repository**
   ```bash
   git clone <your-repository-url>
   cd todo-app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Create environment file**
   ```bash
   cp .env.example .env
   ```

4. **Configure database in .env**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=todo_app
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **Visit the application**
   Open your browser and go to `http://localhost:8000`

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   └── TaskController.php          # Main controller handling all task operations
├── Models/
│   └── Task.php                    # Task model with business logic
database/
├── migrations/
│   └── xxxx_create_tasks_table.php # Database schema
resources/
├── views/
│   ├── layouts/app.blade.php       # Main layout template
│   └── tasks/index.blade.php       # To-do list interface
routes/
└── web.php                         # Application routes
```

## 🔧 API Endpoints

| Method | URL | Description | Response |
|--------|-----|-------------|----------|
| GET | `/` | Main to-do page | HTML view |
| POST | `/tasks` | Create new task | JSON |
| PUT | `/tasks/{id}/toggle` | Toggle task completion | JSON |
| DELETE | `/tasks/{id}` | Delete task | JSON |
| GET | `/tasks/all` | Get all tasks | JSON |
| GET | `/tasks/active` | Get active tasks only | JSON |

## 🎯 Technical Implementation

### AJAX Functionality
- Uses modern Fetch API for all HTTP requests
- CSRF token automatically included in all requests
- Error handling with user-friendly messages
- Loading states for better UX

### Frontend Features
- **No Page Reloads**: All operations use AJAX
- **Real-time Updates**: UI updates immediately after server responses
- **Smooth Animations**: CSS transitions and keyframe animations
- **Responsive Design**: Mobile-first approach with Bootstrap 5
- **Accessibility**: Proper ARIA labels and semantic HTML

### Backend Architecture
- **MVC Pattern**: Clean separation of concerns
- **Eloquent ORM**: Type-safe database operations
- **Request Validation**: Server-side input validation
- **Resource Controllers**: RESTful API design
- **Database Indexing**: Optimized queries for performance

### Security Features
- **CSRF Protection**: All forms protected against CSRF attacks
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Prevention**: Eloquent ORM protects against SQL injection
- **XSS Prevention**: All user input properly escaped

## 📱 Usage Guide

### Adding Tasks
1. Type your task in the input field
2. Press Enter or click the "Add" button
3. Task appears immediately in the list
4. Duplicate tasks are prevented

### Managing Tasks
- **Mark Complete**: Click the checkbox next to any task
- **Delete Task**: Click the trash icon and confirm deletion
- **View All**: Click "Show All Tasks" to see completed tasks
- **View Active**: Click "Show Active Only" to return to active tasks

### Visual Feedback
- Loading spinners during operations
- Success/error messages for all actions
- Smooth animations for task additions/removals
- Hover effects and visual states

## 🔧 Customization

### Styling
Edit `resources/views/layouts/app.blade.php` to modify:
- Color scheme and gradients
- Animation timings
- Layout and spacing
- Typography

### Functionality
Edit `app/Http/Controllers/TaskController.php` to:
- Add new task properties
- Modify validation rules
- Change business logic
- Add new endpoints

### Database
Create new migrations to:
- Add task categories
- Add due dates
- Add task priorities
- Add user relationships

## 🧪 Testing

### Manual Testing Checklist
- [ ] Add task with Enter key
- [ ] Add task with button click
- [ ] Prevent duplicate tasks
- [ ] Mark task as complete
- [ ] Delete task with confirmation
- [ ] Show all tasks toggle
- [ ] Responsive design on mobile
- [ ] Error handling for network issues

### Automated Testing
```bash
# Run PHP unit tests
php artisan test

# Run feature tests
php artisan test --feature
```

## 🚀 Deployment

### Production Setup
1. **Configure environment**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Set up database**
   ```bash
   php artisan migrate --force
   ```

3. **Configure web server**
   - Point document root to `public/` directory
   - Enable URL rewriting for pretty URLs
   - Set appropriate file permissions

### Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error**
- Check database credentials in `.env`
- Ensure database server is running
- Verify database exists

**CSRF Token Mismatch**
- Clear browser cache
- Regenerate application key
- Check session configuration

**Tasks Not Saving**
- Check browser console for JavaScript errors
- Verify CSRF token is present
- Check server error logs

**Styling Issues**
- Clear browser cache
- Check for CSS/JS errors
- Verify CDN resources are loading

### Debug Mode
Enable debug mode in development:
```env
APP_DEBUG=true
```

View logs:
```bash
tail -f storage/logs/laravel.log
```

## 📞 Support

For questions and support:
- Check the Laravel documentation: https://laravel.com/docs
- Review the issue tracker
- Contact the development team

---

**Built with ❤️ using Laravel 11, Bootstrap 5, and modern JavaScript**