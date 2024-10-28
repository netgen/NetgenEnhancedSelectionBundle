const SELECTORS = {
  root: '.multientry',
  items_container: '.multientry-items',
  item: '.multientry-item',
  add_button: '.multientry_add',
  remove_button: '.icon-close',
};

const EVENTS = {
  PREFIX: 'multientry:',
  add: {
    on: 'add',
    before: 'before:add',
  },
  remove: {
    on: 'remove',
    before: 'before:remove',
  },
  limit: {
    reached: 'limit:reached',
    valid: 'limit:valid',
  },
};

class MultiEntry {
  constructor(element, options = {}) {
    this.parser = new DOMParser();
    this.id = 0;

    this.$element = element;
    this.$element.dataset.instanceId = options.instance_id;

    const externalOptions = {
      ...options,
      ...this.$element.dataset,
    };
    this.options = {
      insert_first: true,
      last_item_can_be_removed: false,
      limit: null,
      show_errors: true,
      error_message: `Max number of items: ${externalOptions.limit}`,
      ...externalOptions,
    };

    this.$items_container = this.$element.querySelector(SELECTORS.items_container);
    this.items_exist = !!this.$items_container.querySelector(SELECTORS.item);
    this.item_template = this.$element.dataset.prototype;

    this.$error = MultiEntry.create_element_from_string(
      `<div class="multientry-error">${this.options.error_message}</div>`
    );
    this.$add_button = this.$element.querySelector(SELECTORS.add_button);
    this.$remove_element = MultiEntry.create_element_from_string('<i class="icon-close"></i>');

    this.setup_dom();
    this.setup_events();
    setTimeout(() => {
      if (this.options.insert_first && !this.items_exist) {
        this.add();
      }
    }, 0);
  }

  next_id() {
    const timestamp = new Date();

    return `${timestamp}${this.id++}`;
  }

  render_item_template() {
    const $template = MultiEntry.create_element_from_string(
      this.item_template.replace(/__name__/g, this.next_id())
    );
    $template.classList.add('multientry-item', 'new');
    $template.append(this.$remove_element);

    $template.querySelector(SELECTORS.remove_button).addEventListener('click', () => {
      this.remove($template);
    });

    return $template;
  }

  setup_dom() {
    this.$element.querySelectorAll(SELECTORS.item).forEach(($item) => {
      $item.append(this.$remove_element);
    });
  }

  setup_events() {
    this.$add_button.addEventListener('click', (event) => {
      event.preventDefault();
      this.add();
    });
  }

  add() {
    if (this.options.limit && this.limit_reached()) {
      return;
    }

    const $newItem = this.render_item_template();
    this.trigger(EVENTS.add.before, { item: $newItem });
    this.$items_container.append($newItem);
    this.trigger(EVENTS.add.on, { item: $newItem });
    this.options.limit && this.limit_check();
  }

  remove($item) {
    if (this.items_count() === 1 && !this.options.last_item_can_be_removed) {
      return;
    }

    this.trigger(EVENTS.remove.before, { item: $item });
    $item.remove();
    this.trigger(EVENTS.remove.on, { item: $item });
    this.options.limit && this.limit_check();
  }

  limit_check() {
    if (this.limit_reached()) {
      this.on_limit_reached();
    } else {
      this.on_limit_valid();
    }
  }

  limit_reached() {
    return this.items_count() >= this.options.limit;
  }

  on_limit_reached() {
    if (this.options.show_errors) {
      this.$items_container.append(this.$error);
    }

    this.$add_button.classList.add('disabled');
    this.trigger(EVENTS.limit.reached);
  }

  on_limit_valid() {
    if (this.options.show_errors) {
      this.$error.remove();
    }

    this.$add_button.classList.remove('disabled');
    this.trigger(EVENTS.limit.valid);
  }

  trigger(suffix, data) {
    const eventName = `${EVENTS.PREFIX}${suffix}`;
    const detail = {
      ...data,
      instance: this,
    };

    const event = new CustomEvent(eventName, { detail });

    this.$element.dispatchEvent(event);
    document.body.dispatchEvent(event);
  }

  items_count() {
    return this.$items_container.querySelectorAll(SELECTORS.item).length;
  }

  static create_element_from_string(elementString) {
    const template = document.createElement('template');
    template.innerHTML = elementString;

    return template.content.firstElementChild;
  }
}

const instances = [];
window.initaliseMultientries = function (options = {}) {
  document.querySelectorAll(SELECTORS.root).forEach(($multientry) => {
    let instance = instances[$multientry.dataset.instanceId];
    if (instance) {
      return;
    }

    instance = new MultiEntry($multientry, {
      ...options,
      instance_id: instances.length,
    });
    instances.push(instance);
  });

  return [...instances];
};

window.runMethodOnAllMultientries = function (methodName) {
  instances.forEach((instance) => {
    instance[methodName] && instance[methodName]();
  });
};
