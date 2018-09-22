# Usage

Register the bundle as usual and mount its routing. 

!! Make sure it is not publicly accessible.

```yaml
forci_lexik_translation_ui:
    # A Service ID that implements
    # Forci\Bundle\LexikTranslationUIBundle\Authorization\TranslationAuthorizationCheckerInterface
    # Optional
    authorization_service: Your\AuthorizationChecker\ServiceId
    # Optional
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

# TODO
- favicon
- logo
- use vueup or something else for notificationsg