# Usage

Register the bundle as usual and mount its routing. 

Make sure it is not publicly accessible.

You need to add the following to your asset packages configuration:

```yaml
framework:
    assets:
        packages:
            forci_lexik_translation_ui:
                json_manifest_path: "%kernel.root_dir%/../web/bundles/forcilexiktranslationui/build/manifest.json"
                
# Optional: 
forci_lexik_translation_ui:
    # A Service ID that implements
    # Forci\Bundle\LexikTranslationUIBundle\Authorization\TranslationAuthorizationCheckerInterface
    authorization_service: Your\AuthorizationChecker\ServiceId
```

Then, make a link somewhere in your app to

```twig
<a href="{{ path('forci_lexik_translation_ui_index') }}">
    Translations
</a>
```

# TODO
- favicon
- logo
- Complete rewrite using @vue/cli ~3.0 and bootstrap-vue, taking advantage of better looks and integration, as well as improved codebase
- Shift+Enter should edit & open previous row for edit