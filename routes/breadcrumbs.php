<?php

use Diglactic\Breadcrumbs\Breadcrumbs;

Breadcrumbs::for('home', function ($trail) {
    $trail->push('Главная', route('home'));
});

Breadcrumbs::for('about', function ($trail) {
    $trail->parent('home');
    $trail->push('О нас', route('about'));
});

Breadcrumbs::for('about.show', function ($trail, $item) {
    $trail->parent('about');
    $trail->push($item->title, route('about.show', $item->slug));
});

Breadcrumbs::for('about.team', function ($trail) {
    $trail->parent('about');
    $trail->push('Команда', route('about.team'));
});

Breadcrumbs::for('about.team.show', function ($trail, $item) {
    $trail->parent('about.team');
    $trail->push($item->title, route('about.team.show', $item->slug));
});

Breadcrumbs::for('about.partners', function ($trail) {
    $trail->parent('about');
    $trail->push('Партнёры', route('about.partners'));
});

Breadcrumbs::for('about.partners.show', function ($trail, $item) {
    $trail->parent('about.partners');
    $trail->push($item->title, route('about.partners.show', $item->slug));
});

Breadcrumbs::for('about.documents', function ($trail) {
    $trail->parent('about');
    $trail->push('Документы', route('about.documents'));
});

Breadcrumbs::for('about.documents.show', function ($trail, $item) {
    $trail->parent('about.documents');
    $trail->push($item->title, route('about.documents.show', $item->slug));
});

Breadcrumbs::for('training', function ($trail) {
    $trail->parent('home');
    $trail->push('Обучение', route('training'));
});

Breadcrumbs::for('training.show', function ($trail, $item) {
    $trail->parent('training');
    $trail->push($item->title, route('training.show', $item->slug));
});

Breadcrumbs::for('consulting', function ($trail) {
    $trail->parent('home');
    $trail->push('Консалтинг', route('consulting'));
});

Breadcrumbs::for('consulting.show', function ($trail, $item) {
    $trail->parent('consulting');
    $trail->push($item->title, route('consulting.show', $item->slug));
});

Breadcrumbs::for('remote', function ($trail) {
    $trail->parent('home');
    $trail->push('Дистанционно', route('remote'));
});

Breadcrumbs::for('remote.show', function ($trail, $item) {
    $trail->parent('remote');
    $trail->push($item->title, route('remote.show', $item->slug));
});

Breadcrumbs::for('resources', function ($trail) {
    $trail->parent('home');
    $trail->push('Полезное', route('resources'));
});

Breadcrumbs::for('resources.show', function ($trail, $item) {
    $trail->parent('resources');
    $trail->push($item->title, route('resources.show', $item->slug));
});

Breadcrumbs::for('resources.important', function ($trail) {
    $trail->parent('resources');
    $trail->push('Важное', route('resources.important'));
});

Breadcrumbs::for('resources.important.show', function ($trail, $item) {
    $trail->parent('resources.important');
    $trail->push($item->title, route('resources.important.show', $item->slug));
});

Breadcrumbs::for('resources.news', function ($trail) {
    $trail->parent('resources');
    $trail->push('Новости', route('resources.news'));
});

Breadcrumbs::for('resources.laws', function ($trail) {
    $trail->parent('resources');
    $trail->push('Законы', route('resources.laws'));
});

Breadcrumbs::for('resources.laws.show', function ($trail, $item) {
    $trail->parent('resources.laws');
    $trail->push($item->title, route('resources.laws.show', $item->slug));
});

Breadcrumbs::for('resources.diplomas', function ($trail) {
    $trail->parent('resources');
    $trail->push('Дипломы', route('resources.diplomas'));
});

Breadcrumbs::for('resources.diplomas.show', function ($trail, $item) {
    $trail->parent('resources.diplomas');
    $trail->push($item->title, route('resources.diplomas.show', $item->slug));
});

Breadcrumbs::for('resources.seminar', function ($trail) {
    $trail->parent('resources');
    $trail->push('Семинары', route('resources.seminar'));
});

Breadcrumbs::for('resources.seminar.show', function ($trail, $item) {
    $trail->parent('resources.seminar');
    $trail->push($item->title, route('resources.seminar.show', $item->slug));
});

Breadcrumbs::for('resources.news.show', function ($trail, $item) {
    $trail->parent('resources.news');
    $trail->push($item->title, route('resources.news.show', $item->slug));
});
