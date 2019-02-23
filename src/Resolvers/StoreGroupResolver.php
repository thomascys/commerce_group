<?php

namespace Drupal\commerce_group\Resolvers;

use Drupal\commerce_store\Resolver\StoreResolverInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class StoreGroupResolver implements StoreResolverInterface {
  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The store storage.
   *
   * @var \Drupal\commerce_store\StoreStorageInterface
   */
  protected $storage;

  /**
   * Constructs a new DefaultStoreResolver object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(RequestStack $request_stack, EntityTypeManagerInterface $entity_type_manager) {
    $this->requestStack = $request_stack;
    $this->storage = $entity_type_manager->getStorage('commerce_store');
  }

  /**
   * {@inheritdoc}
   */
  public function resolve() {
    $current_group = $this->requestStack
      ->getCurrentRequest()->attributes->get('group');

    // No active group was determined.
    if (!$current_group) {
      return NULL;
    }

    $query = $this->storage->getQuery();
    $query->condition('group_entity', $current_group->id());
    $store_ids = $query->execute();
    if (!empty($store_ids)) {
      $store_id = reset($store_ids);
      return $this->storage->load($store_id);
    }
  }

}
