# ScandiPWA ContactGraphQl

Fork of [scandipwa/contact-graphql](https://github.com/scandipwa/contact-graphql) 1.0.0, maintained by Selveq for Magento 2.4.9 and PHP 8.3. Module name and namespace are unchanged, and the package replaces `scandipwa/contact-graphql` at every version, so it installs as a drop-in replacement. Selveq is not affiliated with or endorsed by Scandiweb.

## What it does

- Sends the store's Contact Us email from the `contactForm` mutation, with the sender's address as the reply-to.
- Answers whether Contact Us is enabled, through `contactPageConfig`.
- Puts `contactForm` behind the admin's Contact Us reCAPTCHA setting, so a storefront sends the `X-ReCaptcha` header once it is on, as it does for core's mutations.
- Installs the `contact_us_page_block` CMS block for the contact page, selectable as Contact Us CMS Block under Content customization.

## Install

```sh
composer require selveq/contact-graphql
bin/magento setup:upgrade
```

## License

[OSL-3.0](LICENSE), the license of the original work. Scandiweb's copyright notices are kept in every file, and each file Selveq changed carries a `Modifications © Selveq` notice.
