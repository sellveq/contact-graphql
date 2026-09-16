# Changelog

## 2.0.0

Forked from `scandipwa/contact-graphql` 1.0.0. Module name and namespace are unchanged, and the package replaces `scandipwa/contact-graphql` at every version, so it installs as a drop-in replacement.

- Magento 2.4.9 and PHP 8.3 support.
- Composer type corrected from `magento2-theme` to `magento2-module`, so the module installs where Magento looks for it.
- The admin's Contact Us reCAPTCHA setting now covers `contactForm`; with it on, a storefront must send the token in the `X-ReCaptcha` request header.
- Every field is bounded — name 255, telephone 64, email 254, message 10 000 characters — and an oversized field is refused with a message naming it, instead of being mailed.
- A mail transport failure answers a generic message and is logged, as core's `contactUs` does, instead of forwarding the transport's own message to the caller.
- The install is a data patch that leaves an existing block alone and no longer overwrites a block an administrator chose.
- The block it installs is a neutral placeholder assigned to all store views, instead of Luma sample content on the distro store.
- `contactPageConfig` declares a cache identity returning the store-configuration tag, so the answer is cacheable and an administrator turning Contact Us off purges it.
