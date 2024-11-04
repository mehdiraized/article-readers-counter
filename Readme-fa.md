# Article Readers Counter

**Contributors:** mehdiraized  
**Tags:** thumbnails, media management, image optimization, WordPress plugin, thumbnail remover  
**Requires at least:** 5.0  
**Tested up to:** 6.6.1  
**Stable tag:** 1.1.4  
**Requires PHP:** 7.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

نمایش تعداد خوانندگان آنلاین هر مطلب در وردپرس با قابلیت به‌روزرسانی خودکار.

[🇺🇸 English Documentation](README.md)

## ویژگی‌ها

- 🔄 به‌روزرسانی خودکار تعداد خوانندگان
- 👥 شمارش دقیق بر اساس IP
- 🎨 5 تم مختلف برای نمایش
- 📱 طراحی واکنش‌گرا
- 🌙 پشتیبانی از حالت شب
- 🔌 شورت‌کد ساده برای استفاده
- ⚡ عملکرد بهینه و سریع
- 🔒 امنیت بالا

## ساختار فایل‌ها

```
article-readers-counter/
├── article-readers-counter.php     # فایل اصلی پلاگین
├── assets/                         # فایل‌های CSS و JavaScript
│   ├── css/
│   │   └── style.css              # استایل‌های پلاگین
│   └── js/
│       └── reader-counter.js       # اسکریپت‌های پلاگین
├── includes/                       # کلاس‌های اصلی
│   ├── class-arc-ajax-handler.php  # مدیریت درخواست‌های AJAX
│   ├── class-arc-counter.php      # کلاس اصلی شمارنده
│   └── class-arc-settings.php     # مدیریت تنظیمات
├── languages/                      # فایل‌های ترجمه
│   ├── article-readers-counter-fa_IR.po
│   └── article-readers-counter-fa_IR.mo
├── templates/                      # قالب‌ها
│   └── admin-settings.php         # قالب صفحه تنظیمات
└── README.md                      # همین فایل
```

## نصب

1. پوشه `article-readers-counter` را در مسیر `/wp-content/plugins/` آپلود کنید
2. از منوی "افزونه‌ها" در وردپرس، پلاگین را فعال کنید
3. به بخش "تنظیمات > تعداد خوانندگان" بروید و تنظیمات دلخواه را انجام دهید

## استفاده

### شورت‌کد ساده:

```php
[readers_count]
```

### شورت‌کد با پارامترها:

```php
[readers_count before_text="در حال مطالعه: " after_text=" نفر" theme="boxed"]
```

### استفاده در کد تم:

```php
<?php echo do_shortcode('[readers_count]'); ?>
```

### پارامترهای شورت‌کد:

- `before_text`: متن قبل از شمارنده
- `after_text`: متن بعد از شمارنده
- `theme`: قالب نمایش (default, minimal, boxed, rounded, accent)
- `class`: کلاس CSS سفارشی

## تنظیمات

### تنظیمات عمومی:

- درج خودکار در مطالب
- ثبت تعداد کل بازدیدها

### تنظیمات نمایش:

- متن قبل و بعد از شمارنده
- انتخاب قالب نمایش
- کلاس CSS سفارشی

### تنظیمات پیشرفته:

- فاصله به‌روزرسانی (5 تا 60 ثانیه)
- فاصله پاکسازی (30 تا 300 ثانیه)

## نیازمندی‌ها

- PHP نسخه 7.4 یا بالاتر
- وردپرس نسخه 5.0 یا بالاتر
- مرورگر مدرن با پشتیبانی از JavaScript

## مشارکت

اگر می‌خواهید در توسعه این پلاگین مشارکت کنید:

1. این مخزن را Fork کنید
2. یک شاخه برای ویژگی جدید ایجاد کنید (`git checkout -b feature/amazing-feature`)
3. تغییرات خود را commit کنید (`git commit -m 'Add some amazing feature'`)
4. به شاخه اصلی Push کنید (`git push origin feature/amazing-feature`)
5. یک Pull Request ایجاد کنید

## مجوز

این پروژه تحت مجوز GPL نسخه 2 یا بالاتر منتشر شده است.

## پشتیبانی

برای گزارش مشکل یا پیشنهاد ویژگی جدید:

1. به بخش Issues در GitHub مراجعه کنید
2. یک Issue جدید ایجاد کنید
3. مشکل یا پیشنهاد خود را با جزئیات کامل شرح دهید

## تغییرات

### نسخه 1.0.0

- انتشار اولیه
- 5 تم مختلف برای نمایش
- پشتیبانی از زبان فارسی
- سیستم به‌روزرسانی خودکار
- پنل تنظیمات کامل

## سازنده

[نام شما](لینک گیت‌هاب یا وب‌سایت شما)

## قدردانی

از تمام کسانی که در توسعه این پلاگین مشارکت داشته‌اند سپاسگزاریم.
