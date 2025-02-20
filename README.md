# Mage2 Module Kowal Attributes Query

    ``kowal/module-attributesquery``

- [Main Functionalities](#markdown-header-main-functionalities)
- [Installation](#markdown-header-installation)



## Main Functionalities
Export Products to CSV

## Installation
\* = in production please use the `--keep-generated` option

### Type: Composer

1. **Add the composer repository to the configuration:**
   ```bash
   composer config repositories.attributes.query vcs https://gitlab.com/magento2ext/magento-2-attributes-query

2. **Add an access token for the private GitLab repository:**
   ```bash
   composer config --auth gitlab-token.gitlab.com <YOUR_TOKEN>

3. **Install the module using Composer:**
   ```bash
   composer require kowal/module-attributesquery

4. **Enable the module:**
   ```bash
   php bin/magento module:enable Kowal_AttributesQuery

5. **Apply database updates:**
   ```bash
   php bin/magento setup:upgrade
6. **Flush the cache:**
   ```bash
   php bin/magento cache:flush


