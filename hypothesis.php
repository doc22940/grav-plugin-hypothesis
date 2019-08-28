<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;
use Grav\Common\Theme;

/**
 * Class HypothesisPlugin
 * @package Grav\Plugin
 */
class HypothesisPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onGetPageTemplates' => ['onGetPageTemplates', 0]
        ];
    }

    public function onGetPageTemplates($event)
    {
      $types = $event->types;
      $locator = $this->grav['locator'];
      $types->scanBlueprints($locator->findResource('plugin://' . $this->name . '/blueprints'));
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in
        $this->enable([
            'onPageContentRaw' => ['onPageContentRaw', 0]
        ]);
    }

    /**
     * Do some work for this event, full details of events can be found
     * on the learn site: http://learn.getgrav.org/plugins/event-hooks
     *
     * @param Event $e
     */
    public function onPageContentRaw(Event $e)
    {
        $page = $this->grav['page'];
        $header = $page->header();

        if ($this->grav['page']->template() == 'presentation' or $this->grav['page']->template() == 'slide') {
          return;
        }

        if (isset($header->hide_hypothesis)) {
          $excludes = $header->hide_hypothesis;
          if ($excludes)
          return;
        }

        // Get a variable from the plugin configuration
        //$text = $this->grav['config']->get('plugins.hypothesis.text_var');
        $post_text = '<script async defer src="https://hypothes.is/embed.js"></script>';

        // Get the current raw content
        $content = $e['page']->getRawContent();

        // Prepend the output with the custom text and set back on the page
        //$e['page']->setRawContent($text . "\n\n" . $content);
        $e['page']->setRawContent( $content. $post_text );
    }
}
