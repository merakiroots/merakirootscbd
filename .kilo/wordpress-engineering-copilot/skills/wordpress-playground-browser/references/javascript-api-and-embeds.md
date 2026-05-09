# JavaScript API and Embeds

Use this reference when the task involves embedding Playground in a page or controlling it from browser-side JavaScript.

## Core model

- `index.html` is the full app experience
- `remote.html` is the API-oriented runtime endpoint
- `@wp-playground/client` can boot and control Playground in an iframe

## Minimal embed shape

```html
<iframe id="wp"></iframe>
<script type="module">
  import { startPlaygroundWeb } from 'https://playground.wordpress.net/client/index.js';

  const client = await startPlaygroundWeb({
    iframe: document.getElementById('wp'),
    remoteUrl: 'https://playground.wordpress.net/remote.html',
  });

  await client.isReady();
</script>
```

## Use cases

- Embedded plugin or theme demos
- Shareable browser repros
- Docs pages with live WordPress examples
- PHP snippet or Playground-backed educational content

