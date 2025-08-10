// Helper function untuk route yang kompatibel dengan Inertia.js
export function route(name, params = {}) {
  const routes = {
    'login': '/login',
    'dashboard': '/dashboard',
    'knowledge.index': '/knowledge',
    'knowledge.create': '/knowledge/create',
    'knowledge.show': (id) => `/knowledge/${id}`,
    'knowledge.edit': (id) => `/knowledge/${id}/edit`,
    'knowledge.store': '/knowledge',
    'knowledge.update': (id) => `/knowledge/${id}`,
    'knowledge.destroy': (id) => `/knowledge/${id}`,
    'knowledge.search': '/knowledge/search',
    'knowledge.export': '/knowledge/export',
                'knowledge.statistics': '/knowledge/statistics',
            'ai.index': '/ai',
            'users.index': '/users',
            'users.create': '/users/create',
            'users.edit': (id) => `/users/${id}/edit`
  };

  const route = routes[name];

  if (typeof route === 'function') {
    return route(params);
  }

  return route || '/';
}
