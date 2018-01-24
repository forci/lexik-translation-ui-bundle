Register the bundle as usual and mount its routing. 

Make sure it is not publicly accessible.

You need to add the following to your asset packages configuration:

```yaml
framework:
    assets:
        packages:
            forci_lexik_translation_ui:
                json_manifest_path: "%kernel.root_dir%/../web/bundles/forcilexiktranslationui/build/manifest.json"
```

Then, make a link somewhere in your app to

```twig
<a href="{{ path('forci_lexik_translation_ui_index') }}">
    Translations
</a>
```