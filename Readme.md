# Article Readers Counter

**Contributors:** mehdiraized  
**Tags:** thumbnails, media management, image optimization, WordPress plugin, thumbnail remover  
**Requires at least:** 5.0  
**Tested up to:** 6.6.1  
**Stable tag:** 1.1.4  
**Requires PHP:** 7.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Display real-time reader count for WordPress posts with automatic updates.

[🇮🇷 Persian Documentation (مستندات فارسی)](Readme-fa.md)

## Features

- 🔄 Real-time automatic updates
- 👥 Accurate IP-based counting
- 🎨 5 Beautiful display themes
- 📱 Responsive design
- 🌙 Dark mode support
- 🔌 Simple shortcode implementation
- ⚡ Optimized performance
- 🔒 Secure and reliable

## File Structure

```
article-readers-counter/
├── article-readers-counter.php     # Main plugin file
├── assets/                         # CSS and JavaScript files
│   ├── css/
│   │   └── style.css              # Plugin styles
│   └── js/
│       └── reader-counter.js       # Plugin scripts
├── includes/                       # Core classes
│   ├── class-arc-ajax-handler.php  # AJAX request handler
│   ├── class-arc-counter.php      # Main counter class
│   └── class-arc-settings.php     # Settings management
├── languages/                      # Translation files
│   ├── article-readers-counter-fa_IR.po
│   └── article-readers-counter-fa_IR.mo
├── templates/                      # Templates
│   └── admin-settings.php         # Settings page template
└── README.md                      # This file
```

## Installation

1. Upload the `article-readers-counter` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to "Settings > Article Readers" to configure the plugin

## Usage

### Basic Shortcode:

```php
[readers_count]
```

### Shortcode with Parameters:

```php
[readers_count before_text="Currently reading: " after_text=" readers" theme="boxed"]
```

### Theme Integration:

```php
<?php echo do_shortcode('[readers_count]'); ?>
```

### Available Parameters:

- `before_text`: Text to display before the counter
- `after_text`: Text to display after the counter
- `theme`: Display theme (default, minimal, boxed, rounded, accent)
- `class`: Custom CSS class

## Configuration

### General Settings:

- Auto-insert into posts
- Track total view counts

### Display Settings:

- Before and after text
- Counter theme selection
- Custom CSS class

### Advanced Settings:

- Refresh interval (5 to 60 seconds)
- Cleanup interval (30 to 300 seconds)

## Requirements

- PHP 7.4 or higher
- WordPress 5.0 or higher
- Modern browser with JavaScript support

## Theme Support

The plugin provides several themes out of the box:

1. Default Theme

```php
[readers_count theme="default"]
```

2. Minimal Theme

```php
[readers_count theme="minimal"]
```

3. Boxed Theme

```php
[readers_count theme="boxed"]
```

4. Rounded Theme

```php
[readers_count theme="rounded"]
```

5. Accent Theme

```php
[readers_count theme="accent"]
```

## Performance

The plugin is optimized for performance:

- Efficient database queries
- Caching implementation
- Minimal impact on page load
- Automatic cleanup of old records

## Security Features

- Nonce verification
- Data sanitization
- XSS protection
- IP validation
- Rate limiting

## Contributing

Want to contribute? Here's how:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Localization

The plugin is translation-ready and includes English and Persian (Farsi) languages by default.

To add a new translation:

1. Use Poedit to open `languages/article-readers-counter.pot`
2. Create a new translation
3. Save it as `article-readers-counter-{locale}.po`
4. Generate the .mo file

## License

This project is licensed under the GPL v2 or later - see the [LICENSE.md](LICENSE.md) file for details.

## Support

For support:

1. Visit our [GitHub Issues](https://github.com/yourusername/article-readers-counter/issues)
2. Create a new issue
3. Provide detailed information about your problem or suggestion

## Changelog

### Version 1.0.0 (2024-03-20)

- Initial release
- 5 display themes
- Multi-language support
- Auto-update system
- Complete settings panel

### Version 1.0.1 (2024-03-21)

- Performance improvements
- Bug fixes
- Added theme customization options
- Enhanced security measures

## Credits

- Author: [Your Name](https://github.com/yourusername)
- Contributors: [List of contributors]
- Icons: [Icon attribution if any]

## FAQ

**Q: How accurate is the reader count?**  
A: The counter uses IP-based tracking with automatic cleanup of inactive sessions for accurate counting.

**Q: Will this plugin slow down my site?**  
A: No, the plugin is optimized for performance with minimal database queries and efficient caching.

**Q: Can I customize the display style?**  
A: Yes, you can choose from 5 built-in themes or use custom CSS classes.

## Roadmap

- [ ] Add more display themes
- [ ] Advanced analytics dashboard
- [ ] Export functionality
- [ ] API integration
- [ ] Real-time statistics

## Support Us

If you find this plugin useful, please:

- ⭐ Star this repository
- 📢 Share it with others
- 🐛 Report issues
- 🤝 Contribute to development

## Contact

- Website: [your-website.com](https://your-website.com)
- Twitter: [@yourusername](https://twitter.com/yourusername)
- Email: your@email.com

---

Made with ❤️ for the WordPress community
