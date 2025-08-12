// Helper function untuk route yang kompatibel dengan Inertia.js
export function route(name, params = {}, routePrefix = null) {
  // Get route prefix from page props if not provided
  if (!routePrefix && typeof window !== 'undefined' && window.route?.prefix) {
    routePrefix = window.route.prefix;
  }

  const baseRoutes = {
    'login': '/login',
    'dashboard': '/dashboard',
    'dashboard.skpd': '/dashboard/skpd',
    'admin.dashboard': '/admin/dashboard',
    'admin.users.index': '/admin/users',
    'admin.users.list': '/admin/users/list',
    'admin.users.create': '/admin/users/create',
    'admin.users.store': '/admin/users',
    'admin.users.edit': (id) => `/admin/users/${id}/edit`,
    'admin.users.update': (id) => `/admin/users/${id}`,
    'admin.ai.index': '/admin/ai',
    'ai.index': '/ai',
    'users.index': '/users',
    'users.list': '/users/list',
    'users.create': '/users/create',
    'users.store': '/users',
    'users.edit': (id) => `/users/${id}/edit`,
    'users.update': (id) => `/users/${id}`,
    'logout': '/logout'
  };

  // Knowledge routes with prefix support
  const knowledgeRoutes = {
    'knowledge.index': '/knowledge',
    'knowledge.public': '/knowledge/public',
    'knowledge.public.show': (id) => `/knowledge/public/${id}`,
    'knowledge.create': '/knowledge/create',
    'knowledge.show': (id) => `/knowledge/${id}`,
    'knowledge.edit': (id) => `/knowledge/${id}/edit`,
    'knowledge.store': '/knowledge',
    'knowledge.update': (id) => `/knowledge/${id}`,
    'knowledge.delete': (id) => `/knowledge/${id}`,
    'knowledge.destroy': (id) => `/knowledge/${id}`,
    'knowledge.search': '/knowledge/search',
    'knowledge.export': '/knowledge/export',
    'knowledge.statistics': '/knowledge/statistics',
  };

  // Admin knowledge routes
  const adminKnowledgeRoutes = {
    'admin.knowledge.index': '/admin/knowledge',
    'admin.knowledge.create': '/admin/knowledge/create',
    'admin.knowledge.show': (id) => `/admin/knowledge/${id}`,
    'admin.knowledge.edit': (id) => `/admin/knowledge/${id}/edit`,
    'admin.knowledge.store': '/admin/knowledge',
    'admin.knowledge.update': (id) => `/admin/knowledge/${id}`,
    'admin.knowledge.delete': (id) => `/admin/knowledge/${id}`,
    'admin.knowledge.destroy': (id) => `/admin/knowledge/${id}`,
    'admin.knowledge.search': '/admin/knowledge/search',
    'admin.knowledge.export': '/admin/knowledge/export',
    'admin.knowledge.statistics': '/admin/knowledge/statistics',
    'admin.knowledge.verify': (id) => `/admin/knowledge/${id}/verify`,
    'admin.knowledge.unverify': (id) => `/admin/knowledge/${id}/unverify`,
  };

  // SKPD knowledge routes
  const skpdKnowledgeRoutes = {
    'skpd.knowledge.index': '/skpd/knowledge',
    'skpd.knowledge.create': '/skpd/knowledge/create',
    'skpd.knowledge.show': (id) => `/skpd/knowledge/${id}`,
    'skpd.knowledge.edit': (id) => `/skpd/knowledge/${id}/edit`,
    'skpd.knowledge.store': '/skpd/knowledge',
    'skpd.knowledge.update': (id) => `/skpd/knowledge/${id}`,
    'skpd.knowledge.delete': (id) => `/skpd/knowledge/${id}`,
    'skpd.knowledge.destroy': (id) => `/skpd/knowledge/${id}`,
    'skpd.knowledge.search': '/skpd/knowledge/search',
  };

  // Admin knowledge version routes
  const adminKnowledgeVersionRoutes = {
    'admin.knowledge-versions.index': '/admin/knowledge-versions',
    'admin.knowledge-versions.create': '/admin/knowledge-versions/create',
    'admin.knowledge-versions.show': (id) => `/admin/knowledge-versions/${id}`,
    'admin.knowledge-versions.edit': (id) => `/admin/knowledge-versions/${id}/edit`,
    'admin.knowledge-versions.store': '/admin/knowledge-versions',
    'admin.knowledge-versions.update': (id) => `/admin/knowledge-versions/${id}`,
    'admin.knowledge-versions.destroy': (id) => `/admin/knowledge-versions/${id}`,
    'admin.knowledge-versions.update-status': (id) => `/admin/knowledge-versions/${id}/status`,
    'admin.knowledge-versions.publish': (id) => `/admin/knowledge-versions/${id}/publish`,
    'admin.knowledge-versions.archive': (id) => `/admin/knowledge-versions/${id}/archive`,
    'admin.knowledge-versions.verify': (id) => `/admin/knowledge-versions/${id}/verify`,
    'admin.knowledge-versions.reject': (id) => `/admin/knowledge-versions/${id}/reject`,
    'admin.knowledge-versions.download-attachment': (id) => `/admin/knowledge-versions/attachments/${id}/download`,
  };

  // Change Log routes
  const changeLogRoutes = {
    'admin.change-logs.index': '/admin/change-logs',
    'admin.change-logs.knowledge': (id) => `/admin/change-logs/knowledge/${id}`,
    'admin.change-logs.statistics': '/admin/change-logs/statistics',
    'admin.change-logs.trends': '/admin/change-logs/trends',
    'admin.change-logs.recent': '/admin/change-logs/recent',
    'admin.change-logs.user': (userId) => `/admin/change-logs/user/${userId}`,
    'admin.change-logs.export': '/admin/change-logs/export',
    'admin.change-logs.clean': '/admin/change-logs/clean',
  };

  const allRoutes = {
    ...baseRoutes,
    ...knowledgeRoutes,
    ...adminKnowledgeRoutes,
    ...adminKnowledgeVersionRoutes,
    ...skpdKnowledgeRoutes,
    ...changeLogRoutes
  };

  // Auto-redirect knowledge routes based on route prefix
  if (name.startsWith('knowledge.') && routePrefix) {
    if (routePrefix === 'admin') {
      name = name.replace('knowledge.', 'admin.knowledge.');
    } else if (routePrefix === 'skpd') {
      name = name.replace('knowledge.', 'skpd.knowledge.');
    }
  }

  const route = allRoutes[name];

  if (typeof route === 'function') {
    return route(params);
  }

  return route || '/';
}
