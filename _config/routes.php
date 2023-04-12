<?php

$router = [];

/*
 * App routes
 */
$router['app'] = [
    ['namespace' => 'app', 'route' => '/', 'controller' => 'index', 'action' => 'index'],
    ['namespace' => 'app', 'route' => '/empresa', 'controller' => 'about', 'action' => 'index'],

    ['namespace' => 'app', 'route' => '/blog', 'controller' => 'blog', 'action' => 'index'],
    ['namespace' => 'app', 'route' => '/post', 'controller' => 'blog', 'action' => 'read'],
    ['namespace' => 'app', 'route' => '/blog/subcategorias', 'controller' => 'blog', 'action' => 'subcategories'],
    ['namespace' => 'app', 'route' => '/blog/busca', 'controller' => 'blog', 'action' => 'search'],

    ['namespace' => 'app', 'route' => '/fale-conosco', 'controller' => 'contact', 'action' => 'index'],
    ['namespace' => 'app', 'route' => '/fale-conosco/enviar-mensagem', 'controller' => 'contact', 'action' => 'send-message'],

    ['namespace' => 'app', 'route' => '/cadastro-newsletter', 'controller' => 'index', 'action' => 'insert-newsletter'],
    ['namespace' => 'app', 'route' => '/cancelar-inscricao', 'controller' => 'index', 'action' => 'cancel-newsletter'],

    ['namespace' => 'app', 'route' => '/servicos', 'controller' => 'services', 'action' => 'index'],
    ['namespace' => 'app', 'route' => '/servico', 'controller' => 'services', 'action' => 'read'],

    ['namespace' => 'app', 'route' => '/politica-de-privacidade', 'controller' => 'privacy', 'action' => 'index'],
    ['namespace' => 'app', 'route' => '/termos-de-uso', 'controller' => 'use-terms', 'action' => 'index'],

    ['namespace' => 'app', 'route' => '/area-clientes', 'controller' => 'customers', 'action' => 'index'],
	['namespace' => 'app', 'route' => '/area-clientes/', 'controller' => 'customers', 'action' => 'index'],
	['namespace' => 'app', 'route' => '/area-clientes/login', 'controller' => 'customers', 'action' => 'user-login'],
	['namespace' => 'app', 'route' => '/area-clientes/token', 'controller' => 'customers', 'action' => 'user-token'],
	['namespace' => 'app', 'route' => '/area-clientes/logout', 'controller' => 'customers', 'action' => 'user-logout'],
	['namespace' => 'app', 'route' => '/area-clientes/cadastro', 'controller' => 'customers', 'action' => 'user-register'],
	['namespace' => 'app', 'route' => '/area-clientes/meus-dados', 'controller' => 'customers', 'action' => 'profile'],
	['namespace' => 'app', 'route' => '/area-clientes/suporte', 'controller' => 'customers', 'action' => 'support'],
	['namespace' => 'app', 'route' => '/area-clientes/esqueci-a-senha', 'controller' => 'customers', 'action' => 'forgot-password'],
	['namespace' => 'app', 'route' => '/area-clientes/token-senha', 'controller' => 'customers', 'action' => 'password-token'],
	['namespace' => 'app', 'route' => '/area-clientes/alterar-senha', 'controller' => 'customers', 'action' => 'change-password'],
];

/*
 * Admin routes
 */
$router['admin'] = [
    ['namespace' => 'admin', 'route' => '/painel', 'controller' => 'index', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/', 'controller' => 'index', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/inicio', 'controller' => 'index', 'action' => 'index'],

    ['namespace' => 'admin', 'route' => '/painel/login', 'controller' => 'login', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/validar-token', 'controller' => 'login', 'action' => 'token-auth'],
    ['namespace' => 'admin', 'route' => '/painel/cancelar-token', 'controller' => 'login', 'action' => 'token-cancel'],
    ['namespace' => 'admin', 'route' => '/painel/esqueci-a-senha', 'controller' => 'login', 'action' => 'forgot-password'],
    ['namespace' => 'admin', 'route' => '/painel/validar-codigo', 'controller' => 'login', 'action' => 'code-validation'],
    ['namespace' => 'admin', 'route' => '/painel/alterar-senha', 'controller' => 'login', 'action' => 'change-password'],
    ['namespace' => 'admin', 'route' => '/painel/auth',  'controller' => 'login', 'action' => 'auth'],
    ['namespace' => 'admin', 'route' => '/painel/sair',  'controller' => 'login', 'action' => 'logout'],

    ['namespace' => 'admin', 'route' => '/painel/usuarios', 'controller' => 'user', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/usuarios/lista', 'controller' => 'user', 'action' => 'list'],
    ['namespace' => 'admin', 'route' => '/painel/usuarios/detalhes', 'controller' => 'user', 'action' => 'read'],
    ['namespace' => 'admin', 'route' => '/painel/usuarios/cadastrar', 'controller' => 'user', 'action' => 'create'],
    ['namespace' => 'admin', 'route' => '/painel/usuarios/processa-cadastro', 'controller' => 'user', 'action' => 'create-process'],
    ['namespace' => 'admin', 'route' => '/painel/usuarios/editar', 'controller' => 'user', 'action' => 'update'],
    ['namespace' => 'admin', 'route' => '/painel/usuarios/processa-edicao', 'controller' => 'user', 'action' => 'update-process'],
    ['namespace' => 'admin', 'route' => '/painel/usuarios/excluir', 'controller' => 'user', 'action' => 'delete'],
    ['namespace' => 'admin', 'route' => '/painel/usuarios/valor-existente', 'controller' => 'user', 'action' => 'field-exists'],
    ['namespace' => 'admin', 'route' => '/painel/usuarios/acl', 'controller' => 'user', 'action' => 'acl'],
    ['namespace' => 'admin', 'route' => '/painel/usuarios/altera-permissao', 'controller' => 'user', 'action' => 'alter-privilege'],

    ['namespace' => 'admin', 'route' => '/painel/controle-acesso', 'controller' => 'role', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/controle-acesso/lista', 'controller' => 'role', 'action' => 'list'],
    ['namespace' => 'admin', 'route' => '/painel/controle-acesso/detalhes', 'controller' => 'role', 'action' => 'read'],
    ['namespace' => 'admin', 'route' => '/painel/controle-acesso/cadastrar', 'controller' => 'role', 'action' => 'create'],
    ['namespace' => 'admin', 'route' => '/painel/controle-acesso/processa-cadastro', 'controller' => 'role', 'action' => 'create-process'],
    ['namespace' => 'admin', 'route' => '/painel/controle-acesso/editar', 'controller' => 'role', 'action' => 'update'],
    ['namespace' => 'admin', 'route' => '/painel/controle-acesso/processa-edicao', 'controller' => 'role', 'action' => 'update-process'],
    ['namespace' => 'admin', 'route' => '/painel/controle-acesso/excluir', 'controller' => 'role', 'action' => 'delete'],
    ['namespace' => 'admin', 'route' => '/painel/controle-acesso/valor-existente', 'controller' => 'role', 'action' => 'field-exists'],

    ['namespace' => 'admin', 'route' => '/painel/controle-acesso/permissoes', 'controller' => 'privilege', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/controle-acesso/altera-permissao', 'controller' => 'privilege', 'action' => 'change-privilege'],

    ['namespace' => 'admin', 'route' => '/painel/configuracoes', 'controller' => 'config', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/configuracoes/processa-edicao', 'controller' => 'config', 'action' => 'update-process'],

    ['namespace' => 'admin', 'route' => '/painel/politica-privacidade', 'controller' => 'politics', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/politica-privacidade/processa-edicao', 'controller' => 'politics', 'action' => 'update-process'],

    ['namespace' => 'admin', 'route' => '/painel/termos-de-uso', 'controller' => 'use-terms', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/termos-de-uso/processa-edicao', 'controller' => 'use-terms', 'action' => 'update-process'],

    ['namespace' => 'admin', 'route' => '/painel/smtp', 'controller' => 'smtp', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/smtp/processa-edicao', 'controller' => 'smtp', 'action' => 'update-process'],

    ['namespace' => 'admin', 'route' => '/painel/logs', 'controller' => 'logs', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/logs/detalhes', 'controller' => 'logs', 'action' => 'read'],

    ['namespace' => 'admin', 'route' => '/painel/meu-perfil', 'controller' => 'my-profile', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/meu-perfil/processa-edicao', 'controller' => 'my-profile', 'action' => 'update-process'],
    ['namespace' => 'admin', 'route' => '/painel/meu-perfil/valor-existente', 'controller' => 'my-profile', 'action' => 'field-exists'],

    ['namespace' => 'admin', 'route' => '/painel/seo', 'controller' => 'seo', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/seo/processa-edicao', 'controller' => 'seo', 'action' => 'update-process'],

    ['namespace' => 'admin', 'route' => '/painel/banner', 'controller' => 'banner', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/banner/detalhes', 'controller' => 'banner', 'action' => 'read'],
    ['namespace' => 'admin', 'route' => '/painel/banner/cadastrar', 'controller' => 'banner', 'action' => 'create'],
    ['namespace' => 'admin', 'route' => '/painel/banner/processa-cadastro', 'controller' => 'banner', 'action' => 'create-process'],
    ['namespace' => 'admin', 'route' => '/painel/banner/editar', 'controller' => 'banner', 'action' => 'update'],
    ['namespace' => 'admin', 'route' => '/painel/banner/processa-edicao', 'controller' => 'banner', 'action' => 'update-process'],
    ['namespace' => 'admin', 'route' => '/painel/banner/excluir', 'controller' => 'banner', 'action' => 'delete'],
    ['namespace' => 'admin', 'route' => '/painel/banner/valor-existente', 'controller' => 'banner', 'action' => 'field-exists'],

    ['namespace' => 'admin', 'route' => '/painel/banners-internos', 'controller' => 'internal-banners', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/banners-internos/processa-edicao', 'controller' => 'internal-banners', 'action' => 'update-process'],

    ['namespace' => 'admin', 'route' => '/painel/blog', 'controller' => 'blog', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/blog/detalhes', 'controller' => 'blog', 'action' => 'read'],
    ['namespace' => 'admin', 'route' => '/painel/blog/cadastrar', 'controller' => 'blog', 'action' => 'create'],
    ['namespace' => 'admin', 'route' => '/painel/blog/processa-cadastro', 'controller' => 'blog', 'action' => 'create-process'],
    ['namespace' => 'admin', 'route' => '/painel/blog/editar', 'controller' => 'blog', 'action' => 'update'],
    ['namespace' => 'admin', 'route' => '/painel/blog/processa-edicao', 'controller' => 'blog', 'action' => 'update-process'],
    ['namespace' => 'admin', 'route' => '/painel/blog/excluir', 'controller' => 'blog', 'action' => 'delete'],
    ['namespace' => 'admin', 'route' => '/painel/blog/remove-imagem', 'controller' => 'blog', 'action' => 'delete-file'],
    ['namespace' => 'admin', 'route' => '/painel/blog/valor-existente', 'controller' => 'blog', 'action' => 'field-exists'],
    ['namespace' => 'admin', 'route' => '/painel/blog/cadastrar-categoria', 'controller' => 'blog', 'action' => 'create-category'],
    ['namespace' => 'admin', 'route' => '/painel/blog/cadastrar-subcategoria', 'controller' => 'blog', 'action' => 'create-subcategory'],
    ['namespace' => 'admin', 'route' => '/painel/blog/subcategorias', 'controller' => 'blog', 'action' => 'subcategories'],

    ['namespace' => 'admin', 'route' => '/painel/depoimentos', 'controller' => 'testimonials', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/depoimentos/detalhes', 'controller' => 'testimonials', 'action' => 'read'],
    ['namespace' => 'admin', 'route' => '/painel/depoimentos/cadastrar', 'controller' => 'testimonials', 'action' => 'create'],
    ['namespace' => 'admin', 'route' => '/painel/depoimentos/processa-cadastro', 'controller' => 'testimonials', 'action' => 'create-process'],
    ['namespace' => 'admin', 'route' => '/painel/depoimentos/editar', 'controller' => 'testimonials', 'action' => 'update'],
    ['namespace' => 'admin', 'route' => '/painel/depoimentos/processa-edicao', 'controller' => 'testimonials', 'action' => 'update-process'],
    ['namespace' => 'admin', 'route' => '/painel/depoimentos/excluir', 'controller' => 'testimonials', 'action' => 'delete'],

    ['namespace' => 'admin', 'route' => '/painel/home', 'controller' => 'home', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/home/processa-edicao', 'controller' => 'home', 'action' => 'update-process'],

    ['namespace' => 'admin', 'route' => '/painel/empresa', 'controller' => 'about', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/empresa/processa-edicao', 'controller' => 'about', 'action' => 'update-process'],

    ['namespace' => 'admin', 'route' => '/painel/newsletter', 'controller' => 'newsletter', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/newsletter/detalhes', 'controller' => 'newsletter', 'action' => 'read'],
    ['namespace' => 'admin', 'route' => '/painel/newsletter/excluir', 'controller' => 'newsletter', 'action' => 'delete'],

    ['namespace' => 'admin', 'route' => '/painel/categorias', 'controller' => 'categories', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/categorias/cadastrar', 'controller' => 'categories', 'action' => 'create'],
    ['namespace' => 'admin', 'route' => '/painel/categorias/processa-cadastro', 'controller' => 'categories', 'action' => 'create-process'],
    ['namespace' => 'admin', 'route' => '/painel/categorias/detalhes', 'controller' => 'categories', 'action' => 'read'],
    ['namespace' => 'admin', 'route' => '/painel/categorias/editar', 'controller' => 'categories', 'action' => 'update'],
    ['namespace' => 'admin', 'route' => '/painel/categorias/processa-edicao', 'controller' => 'categories', 'action' => 'update-process'],
    ['namespace' => 'admin', 'route' => '/painel/categorias/excluir', 'controller' => 'categories', 'action' => 'delete'],
    ['namespace' => 'admin', 'route' => '/painel/categorias/valor-existente', 'controller' => 'categories', 'action' => 'field-exists'],

    ['namespace' => 'admin', 'route' => '/painel/subcategorias', 'controller' => 'subcategories', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/subcategorias/cadastrar', 'controller' => 'subcategories', 'action' => 'create'],
    ['namespace' => 'admin', 'route' => '/painel/subcategorias/processa-cadastro', 'controller' => 'subcategories', 'action' => 'create-process'],
    ['namespace' => 'admin', 'route' => '/painel/subcategorias/detalhes', 'controller' => 'subcategories', 'action' => 'read'],
    ['namespace' => 'admin', 'route' => '/painel/subcategorias/editar', 'controller' => 'subcategories', 'action' => 'update'],
    ['namespace' => 'admin', 'route' => '/painel/subcategorias/processa-edicao', 'controller' => 'subcategories', 'action' => 'update-process'],
    ['namespace' => 'admin', 'route' => '/painel/subcategorias/excluir', 'controller' => 'subcategories', 'action' => 'delete'],
    ['namespace' => 'admin', 'route' => '/painel/subcategorias/valor-existente', 'controller' => 'subcategories', 'action' => 'field-exists'],

    ['namespace' => 'admin', 'route' => '/painel/servicos', 'controller' => 'services', 'action' => 'index'],
    ['namespace' => 'admin', 'route' => '/painel/servicos/cadastrar', 'controller' => 'services', 'action' => 'create'],
    ['namespace' => 'admin', 'route' => '/painel/servicos/processa-cadastro', 'controller' => 'services', 'action' => 'create-process'],
    ['namespace' => 'admin', 'route' => '/painel/servicos/detalhes', 'controller' => 'services', 'action' => 'read'],
    ['namespace' => 'admin', 'route' => '/painel/servicos/editar', 'controller' => 'services', 'action' => 'update'],
    ['namespace' => 'admin', 'route' => '/painel/servicos/processa-edicao', 'controller' => 'services', 'action' => 'update-process'],
    ['namespace' => 'admin', 'route' => '/painel/servicos/excluir', 'controller' => 'services', 'action' => 'delete'],
    ['namespace' => 'admin', 'route' => '/painel/servicos/valor-existente', 'controller' => 'services', 'action' => 'field-exists'],
    ['namespace' => 'admin', 'route' => '/painel/servicos/remove-imagem', 'controller' => 'services', 'action' => 'delete-file'],

	['namespace' => 'admin', 'route' => '/painel/clientes', 'controller' => 'customers', 'action' => 'index'],
	['namespace' => 'admin', 'route' => '/painel/clientes/detalhes', 'controller' => 'customers', 'action' => 'read'],
	['namespace' => 'admin', 'route' => '/painel/clientes/cadastrar', 'controller' => 'customers', 'action' => 'create'],
	['namespace' => 'admin', 'route' => '/painel/clientes/processa-cadastro', 'controller' => 'customers', 'action' => 'create-process'],
	['namespace' => 'admin', 'route' => '/painel/clientes/editar', 'controller' => 'customers', 'action' => 'update'],
	['namespace' => 'admin', 'route' => '/painel/clientes/processa-edicao', 'controller' => 'customers', 'action' => 'update-process'],
	['namespace' => 'admin', 'route' => '/painel/clientes/excluir', 'controller' => 'customers', 'action' => 'delete'],
	['namespace' => 'admin', 'route' => '/painel/clientes/valor-existente', 'controller' => 'customers', 'action' => 'field-exists'],
	['namespace' => 'admin', 'route' => '/painel/clientes/busca-cliente', 'controller' => 'customers', 'action' => 'search'],

    ['namespace' => 'admin', 'route' => '/painel/importar', 'controller' => 'importer', 'action' => 'index'],
	['namespace' => 'admin', 'route' => '/painel/importar/nova', 'controller' => 'importer', 'action' => 'create'],
	['namespace' => 'admin', 'route' => '/painel/importar/envia-dados', 'controller' => 'importer', 'action' => 'create-process'],
	['namespace' => 'admin', 'route' => '/painel/importar/detalhes', 'controller' => 'importer', 'action' => 'read'],
	['namespace' => 'admin', 'route' => '/painel/importar/excluir', 'controller' => 'importer', 'action' => 'delete'],
];

$systemDir = match ($_SERVER['HTTP_HOST']) {
    'localhost' => "/plataforma-agendamentos",
    default => "",
};

$admin = new Core\Init\Bootstrap($router, $systemDir);
