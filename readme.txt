=== SmartWriter AI - AI Blog Post Generator ===
Contributors: smartwriter-ai-team
Tags: ai, content generation, auto posting, perplexity, openai, blog automation, seo, scheduling
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically generate and schedule WordPress posts using Perplexity AI. Features intelligent scheduling, SEO optimization, and content mapping.

== Description ==

**SmartWriter AI** is a comprehensive WordPress plugin that revolutionizes content creation by leveraging the power of artificial intelligence. Generate high-quality, SEO-optimized blog posts automatically with support for multiple AI providers including Perplexity AI, OpenAI, and Anthropic Claude.

### 🚀 Key Features

**AI Content Generation**
* Support for Perplexity AI, OpenAI, and Anthropic Claude
* Customizable prompt templates with dynamic placeholders
* Intelligent content parsing and structuring
* Real-time content preview

**Advanced Scheduling**
* Flexible posting intervals (30 min to daily)
* Smart time window controls (9 AM - 6 PM by default)
* Backdate posting for content gap filling
* CRON-based automation with fail-safes

**SEO Integration**
* Native support for Yoast SEO, All in One SEO, RankMath
* Automatic meta titles and descriptions
* Focus keyword assignment
* Content readability scoring

**Featured Image Generation**
* Unsplash and Pexels integration for stock photos
* DALL-E AI image generation support
* Automatic image attribution
* Smart image selection based on content

**Professional Dashboard**
* Real-time system health monitoring
* Comprehensive activity logs
* Performance analytics
* Quick action buttons for testing

**Enterprise Features**
* Rate limiting and API usage monitoring
* Bulk content generation
* Content analytics and reporting
* Export/import settings

### 💡 Perfect For

* **Bloggers** - Maintain consistent posting schedules
* **Content Marketers** - Scale content production efficiently
* **SEO Professionals** - Generate optimized content at scale
* **Agencies** - Manage multiple client sites
* **Publishers** - Fill content calendars automatically

### 🎯 Use Cases

1. **Daily News Blogs** - Generate timely content on trending topics
2. **Niche Websites** - Create specialized content with targeted keywords
3. **Corporate Blogs** - Maintain thought leadership with regular posts
4. **E-commerce Sites** - Generate product-related content
5. **Personal Blogs** - Keep your audience engaged with fresh content

### 🔧 Technical Features

* **WordPress Standards Compliant** - Follows all WordPress coding standards
* **Secure API Handling** - Encrypted API key storage
* **Performance Optimized** - Lightweight with smart caching
* **Mobile Responsive** - Admin interface works on all devices
* **Multilingual Ready** - Translation-ready with proper text domains

== Installation ==

### Automatic Installation

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "SmartWriter AI"
4. Click "Install Now" and then "Activate"

### Manual Installation

1. Download the plugin zip file
2. Upload to `/wp-content/plugins/` directory
3. Extract the files
4. Activate the plugin through the 'Plugins' menu in WordPress

### Quick Setup

1. Go to **SmartWriter AI > Settings**
2. Enter your API key (Perplexity AI, OpenAI, or Anthropic)
3. Configure your prompt template
4. Set your posting schedule
5. Save settings and start generating content!

== Frequently Asked Questions ==

= Which AI providers are supported? =

SmartWriter AI supports:
* **Perplexity AI** (Recommended) - Llama 3.1 Sonar models
* **OpenAI** - GPT-3.5 Turbo, GPT-4, GPT-4 Turbo
* **Anthropic** - Claude 3 Sonnet, Opus, and Haiku

= How do I get an API key? =

**For Perplexity AI:**
1. Visit https://www.perplexity.ai/settings/api
2. Create an account or sign in
3. Generate a new API key

**For OpenAI:**
1. Visit https://platform.openai.com/api-keys
2. Create an account or sign in
3. Click "Create new secret key"

**For Anthropic:**
1. Visit https://console.anthropic.com/
2. Create an account or sign in
3. Generate a new API key

= Can I customize the content style? =

Yes! SmartWriter AI uses customizable prompt templates with placeholders:
* `{topic}` - Dynamic topic insertion
* `{date}` - Current date
* `{keyword}` - SEO keyword targeting
* `{category}` - Post category
* `{author}` - Author name

= Is my API key secure? =

Absolutely. API keys are:
* Stored using WordPress's secure options API
* Sanitized and encrypted
* Never displayed in plain text
* Only accessible to admin users

= Can I generate images for posts? =

Yes! SmartWriter AI supports:
* **Unsplash** - High-quality stock photos (free)
* **Pexels** - Professional stock photos (free)
* **DALL-E** - AI-generated custom images (requires OpenAI API)

= How does the scheduling work? =

The plugin uses WordPress CRON to:
* Generate posts at specified intervals
* Respect time windows (e.g., 9 AM - 6 PM)
* Handle daily post limits
* Process backdated content
* Manage failed generations gracefully

= Which SEO plugins are supported? =

Native integration with:
* **Yoast SEO** - Meta titles, descriptions, focus keywords
* **All in One SEO** - Complete meta data integration
* **RankMath** - SEO optimization and scoring
* **Generic fallback** - Basic meta tag support

= Can I schedule posts for the past? =

Yes! The backdate feature allows you to:
* Fill content gaps in your posting history
* Generate posts for specific date ranges
* Maintain consistent content density
* Improve your site's content timeline

= What happens if content generation fails? =

SmartWriter AI includes robust error handling:
* Automatic retry mechanisms
* Detailed error logging
* Rate limit respect
* Graceful degradation
* Admin notifications for critical issues

= Can I preview content before publishing? =

Yes! Features include:
* Real-time content preview
* Test content generation
* Draft mode for manual review
* Content validation checks

== Screenshots ==

1. **Main Dashboard** - Overview of system status, recent posts, and quick actions
2. **Settings Panel** - Comprehensive configuration with tabbed interface
3. **API Configuration** - Easy setup for multiple AI providers
4. **Content Preview** - Test and preview generated content
5. **Scheduling Interface** - Flexible posting schedule configuration
6. **System Health** - Real-time monitoring and diagnostics
7. **Activity Logs** - Detailed logging and debugging information

== Changelog ==

= 1.0.0 =
* Initial release
* Support for Perplexity AI, OpenAI, and Anthropic Claude
* Advanced scheduling with time windows
* SEO plugin integration (Yoast, AIOSEO, RankMath)
* Featured image generation (Unsplash, Pexels, DALL-E)
* Comprehensive admin dashboard
* Activity logging and system health monitoring
* Backdate posting capability
* Content preview and testing
* Rate limiting and error handling
* Mobile-responsive admin interface

== Upgrade Notice ==

= 1.0.0 =
Initial release of SmartWriter AI. Start generating AI-powered content for your WordPress site!

== Developer Information ==

### API Requirements

**Minimum API Quotas:**
* Perplexity AI: 20 requests/minute (recommended)
* OpenAI: 60 requests/minute
* Anthropic: 50 requests/minute

### Hooks and Filters

**Actions:**
* `smartwriter_ai_post_created` - Fired when a post is created
* `smartwriter_ai_content_generated` - Fired when content is generated
* `smartwriter_ai_scheduler_run` - Fired when scheduler runs

**Filters:**
* `smartwriter_ai_prompt_template` - Modify prompt template
* `smartwriter_ai_generated_content` - Modify generated content
* `smartwriter_ai_post_data` - Modify post data before creation

### Database Tables

The plugin creates two custom tables:
* `wp_smartwriter_logs` - Activity logging
* `wp_smartwriter_scheduled_posts` - Scheduled post queue

### Performance Considerations

* Uses WordPress transients for caching
* Implements proper rate limiting
* Optimized database queries
* Minimal resource footprint
* Background processing for heavy operations

### Security Features

* Nonce verification for all AJAX requests
* Capability checks for admin functions
* Input sanitization and output escaping
* Secure API key storage
* SQL injection prevention

== Support ==

For support, documentation, and updates:

* **Documentation**: Visit our comprehensive documentation
* **Community**: Join our community forum
* **Bug Reports**: Submit issues on GitHub
* **Feature Requests**: Vote on new features
* **Premium Support**: Available for enterprise users

== Privacy & Data ==

SmartWriter AI respects your privacy:
* No user data is sent to third parties (except chosen AI provider)
* API keys are stored securely and never shared
* Generated content remains on your server
* Logs contain no sensitive information
* Compliant with GDPR and privacy regulations

== Credits ==

SmartWriter AI is developed with love for the WordPress community. Special thanks to:
* WordPress Core Team for the excellent foundation
* AI providers for making advanced AI accessible
* Open source community for inspiration and tools
* Beta testers for feedback and suggestions

---

**Start your AI-powered content journey today with SmartWriter AI!**