<?php
/**
 * Theme Customizer
 */

if (!defined('ABSPATH')) {
    exit;
}

function tetradkata_customize_register($wp_customize) {
    /**
     * Hero Section
     */
    $wp_customize->add_section('hero_section', array(
        'title' => __('Hero Section', 'tetradkata'),
        'priority' => 30,
    ));
    
    $wp_customize->add_setting('hero_title', array(
        'default' => 'Тетрадката',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_title', array(
        'label' => __('Hero Title', 'tetradkata'),
        'section' => 'hero_section',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('hero_subtitle', array(
        'default' => 'Колекция от спомени',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_subtitle', array(
        'label' => __('Hero Subtitle', 'tetradkata'),
        'section' => 'hero_section',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('hero_description', array(
        'default' => 'Личният ви дневник за пътешествия, спомени и вдъхновение – събрани в една тетрадка.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('hero_description', array(
        'label' => __('Hero Description', 'tetradkata'),
        'section' => 'hero_section',
        'type' => 'textarea',
    ));
    
    /**
     * Contact Section
     */
    $wp_customize->add_section('contact_section', array(
        'title' => __('Contact Information', 'tetradkata'),
        'priority' => 40,
    ));
    
    $wp_customize->add_setting('contact_phone', array(
        'default' => '+359 888 123 456',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('contact_phone', array(
        'label' => __('Phone', 'tetradkata'),
        'section' => 'contact_section',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('contact_email', array(
        'default' => 'info@tetradkata.bg',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('contact_email', array(
        'label' => __('Email', 'tetradkata'),
        'section' => 'contact_section',
        'type' => 'email',
    ));
}
add_action('customize_register', 'tetradkata_customize_register');
?>