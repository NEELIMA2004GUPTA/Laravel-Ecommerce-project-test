<!--! Shoppix - Laravel eCommerce Platform  -->
Shoppix is a fully functional eCommerce platform built using Laravel, designed to provide a seamless shopping experience for users while offering comprehensive management capabilities for administrators.

<!--! Project Goal -->

- The goal of Shoppix is to build a complete eCommerce system where:
- Users can browse products, add them to the cart, checkout, and place orders.
- Admins can manage products, categories, users, and orders efficiently.
- Customers can apply coupons, write product reviews, and track their order history.

<!--! Project Modules & Features -->
1. Authentication & User Management
## Users:
- Register, Login, Forgot Password
- Profile Management (update info and password)

## Admins:
- Separate login and dashboard
- Manage users (block/unblock, view orders, assign roles)

2. Product & Category Management (Admin)
- CRUD for Products: Title, Description, Price, Discount, SKU, Stock Quantity
- Multiple Images and Videos using Laravel File Storage
- Product Variants (Size, Color)
- CRUD for Categories and Subcategories

3. Product Browsing & Search (Users)
- Home Page showing featured or latest products 
- Product Detail Pages
- Product Search and Filters (by category, price range, etc.)

4. Shopping Cart & Wishlist
- Add, Remove, and Update items in the cart (session based)
- Add products to Wishlist (only for logged-in users)
- View Cart summary (subtotal, total, tax, etc.)

5. Checkout & Orders
- Checkout form including address, payment method, and notes
- Order summary before confirmation
- After checkout: create order and reduce stock automatically
- Order status management: Pending,Confirmed, Shipped, Delivered, Cancelled

6. Order History & Admin Dashboard
## For Users:
- View past orders and track status
 
## Admins:
- Dashboard with sales statistics, total users, most sold products
- Manage Orders (update status, view details)

7. Reviews & Ratings (Optional)
- Logged-in users can post product reviews
- Display average rating on product pages
- Users can upload images, prerecorded videoes and record real-time video reviews using WebRTC

8. Coupons & Discounts (Optional)
- Admin can create discount codes
- Apply coupons at checkout
- Validate expiry and usage limits

9. Notifications & Emails
- Email notifications after order confirmation
- Admin notification for new orders

10. Deployment & Optimization
- Use .env for configuration
- Docker support for local development (Dockerfile + docker-compose)
- Optimized routes, caching, and database queries for performance

<!--! Technology Stack -->
- **Backend**: Laravel 10
- **Frontend**: Blade Templates 
- **Database**: MySQL 
- **Authentication**: Laravel Breeze 
- **Docker**: For containerized development

<!--! Installation & Setup -->
- Clone the repository:
    git clone 
    cd laravel-ecommerce
- Install dependencies:
    composer install
    npm install && npm run dev
- Copy .env file and generate application key:
    cp .env.example .env
    php artisan key:generate
- Configure database in .env
- Configure mail in .env
- Run migrations and seeders:
    php artisan migrate --seed
- Start the development server:
    php artisan serve

<!--! Docker Usage -->
Shoppix can be run in a Docker container for easy setup:

1. Build Docker image:
    docker-compose build
2. Start containers:
    docker-compose up -d
3. Stop containers:
    docker-compose down
4. Access the application:
   From your local machine: http://localhost:8000
   Inside the container (port 80): http://localhost

<!--! Screenshot of the running container -->
![DockerContainer](laravel-ecommerce\screenshots\Screenshot (15).png)

<!-- HAPPY CODING -->
