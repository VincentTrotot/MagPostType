<?php

namespace VincentTrotot\Mag;

use Timber\Timber;
use Symfony\Component\HttpFoundation\Request;

class MagPostType
{
    public function __construct()
    {
        add_action('init', [$this, 'createPosType']);
        add_filter('manage_edit-vt_kiosque_columns', [$this, 'editColumns']);
        add_action('admin_init', [$this, 'setupMetabox']);

        add_action('save_post', [$this, 'save']);

        add_filter('post_updated_messages', [$this, 'updatedMessages']);
    }

    /**
     * Création du custom post type  \
     * hook: init
     */
    public function createPostype()
    {
        $labels = [
            'name' => _x('Un mois à Grans', 'kiosque'),
            'all_items' => __('Tous les mois à Grans'),
            'singular_name' => _x('Un mois à Grans', 'kiosque'),
            'add_new' => _x('Ajouter un magazine', 'kiosque'),
            'add_new_item' => __('Ajouter un magazine'),
            'edit_item' => __('Modifier le magazine'),
            'new_item' => __('Nouveau magazine'),
            'view_item' => __('Voir le magazine'),
            'search_items' => __('Rechercher dans les magazine'),
            'not_found' =>  __('Pas de magazine trouvé'),
            'not_found_in_trash' => __('Pas de magazine trouvé dans la corbeille'),
            'parent_item_colon' => '',
        ];

        $args = [
            'label' => __('Magazines'),
            'labels' => $labels,
            'public' => true,
            'can_export' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            '_builtin' => false,
            '_edit_link' => 'post.php?post=%d',
            'capability_type' => 'post',
            'menu_icon' => 'dashicons-format-aside',
            'hierarchical' => false,
            'rewrite' =>[ 'slug' => 'un-mois-a-grans' ],
            'has_archive' => 'un-mois-a-grans',
            'supports'=>['title', 'editor', 'thumbnail', 'excerpt', 'author'] ,
            'show_in_nav_menus' => true,
            'taxonomies' =>[ 'vt_kiosque_category', 'post_tag']
        ];

        register_post_type('vt_kiosque', $args);
    }

    /**
     * Paramétrage des colonnes  \
     * hook: manage_edit-vt_kiosque_columns
     */
    public function editColumns($columns)
    {
        $columns = [
            "cb" => "<input type=\"checkbox\" />",
            "title" => "Magazines",
            ];

        return $columns;
    }

    /**
     * Paramétrage de la meta box  \
     * hook: admin_init
     */
    public function setupMetabox()
    {
        add_meta_box(
            'vt_kiosque_meta',
            'Document',
            [$this, 'displayMetabox'],
            'vt_kiosque',
            'side'
        );
    }

    /**
     * Affichage de la meta box  \
     */
    public function displayMetabox()
    {

        $context['post'] = new Mag();
        $context['nonce'] = wp_create_nonce('vt_kiosque-nonce');
        
        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        } else {
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
        }

        Timber::render('templates/kiosque-meta-box.html.twig', $context);
    }

    /**
     * Sauvegrade du post  \
     * hoo: save_post
     */
    public function save()
    {
        global $post;

        // - still require nonce

        if (!isset($_POST['vt_kiosque-nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['vt_kiosque-nonce'], 'vt_kiosque-nonce')) {
            return $post->ID;
        }

        if (!current_user_can('edit_post', $post->ID)) {
            return $post->ID;
        }

        // saving document
        update_post_meta($post->ID, "url", $_POST['url']);
        update_post_meta($post->ID, "name", $_POST['name']);
    }

    /**
     * Paramétrage des messages de mise à jour  \
     * hook: post_updated_messages
     */
    public function updatedMessages($messages)
    {
        global $post, $post_ID;
        $request = new Request($_GET);
        $revision = $request->query->get('revision');


        $messages['vt_kiosque'] = [
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf(__('Page mis à jour. <a href="%s">Voir la page</a>'), esc_url(get_permalink($post_ID))),
            2 => __('Champ mis à jour.'),
            3 => __('Champ supprimé.'),
            4 => __('Page mis à jour.'),
            5 => $revision ? sprintf(
                __('Event restored to revision from %s'),
                wp_post_revision_title((int) $revision, false)
            ) : false,
            6 => sprintf(__('Page publié. <a href="%s">Voir la page</a>'), esc_url(get_permalink($post_ID))),
            7 => __('Page sauvegardé.'),
            8 => sprintf(
                __('Page soumis. <a target="_blank" href="%s">Prévisualiser la page</a>'),
                esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
            ),
            9 => sprintf(
                __(
                    'Page programmé pour : '
                    .'<strong>%1$s</strong>. '
                    .'<a target="_blank" href="%2$s">Prévisualiser la page</a>'
                ),
                date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)),
                esc_url(get_permalink($post_ID))
            ),
            10 => sprintf(
                __('Brouillon de la page mis à jour. <a target="_blank" href="%s">Prévisualiser la page</a>'),
                esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
            ),
        ];

        return $messages;
    }
}
