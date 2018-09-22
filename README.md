# Usage

Register the bundle as usual and mount its routing. 

!! Make sure it is not publicly accessible.

```yaml
forci_lexik_translation_ui:
    # A Service ID that implements
    # Forci\Bundle\LexikTranslationUIBundle\Authorization\TranslationAuthorizationCheckerInterface
    # Optional - example in sample app
    authorization_service: Your\AuthorizationChecker\ServiceId
    # Optional - example in sample app
    description_files:
        - '%kernel.root_dir%/../src/App/Resources/translations_descriptions.yml'
```

```yaml
# translations_descriptions.yml
some.translation.key: Some descriptive text to be displayed inside the UI
```

Then, make a link somewhere in your app to

```twig
<a href="{{ path('forci_lexik_translation_ui_index') }}">
    Translations
</a>
```

# Play with the Sample App

- The `forci_lexik_translation_ui.config.per_page` parameter is hardcoded to 2 for the test application

```bash
cd sample/
./bin/console doctrine:database:create -e prod
./bin/console doctrine:schema:update -e prod -f
./bin/console lexik:translations:import App -e prod
./bin/console server:start
./bin/console server:start --docroot public/
```

If you would like to experiment/develop the user interface:

```bash
cd src/Resources/vue
npm install
npm run serve
// In order for the API to work, you need to start the local symfony server as per the above block
// build production assets
npm run build
```

# TODO
- favicon
- logo
- use vueup or something else for notificationsg