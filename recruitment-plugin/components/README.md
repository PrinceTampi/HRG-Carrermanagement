# Components

Reusable UI belongs here. Organize components by responsibility, then by area when needed:

```text
components/
├── public/
└── admin/
```

Pages should receive data and include components; components should not decide routes or open database connections.