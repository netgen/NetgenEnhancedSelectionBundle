(function($) {
    'use strict';

    //initialize
    var MultiEntry = function(element, options){
       this.opts = $.extend({
          insert_first: true,
          last_item_can_be_removed: false,
          limit: null,
          show_errors: true,
       }, options || {});
       this.id = 0;
       this.$el = $(element);
       $.extend(this.opts, this.$el.data() || {});
       this.$items_container = this.$el.find('.multientry-items');
       this.items_exist = this.$items_container.find('.multientry-item').length ? true : false;
       this.$add_button = this.$el.find('.multientry_add');
       this.item_template = this.$el.data('prototype');
       this.close_element = '<i class="icon-close"></i>';
       this.error_message = this.opts.error_message || ('Max number of items: '+ this.opts.limit);
       this.$error = $('<div class="multientry-error">'+ this.error_message +'</div>');
       this.setup_dom();
       this.setup_events();
       setTimeout($.proxy(function(){
        this.opts.insert_first && !(this.items_exist) && this.add();
       }, this), 0);
    };

    MultiEntry.prototype.next_id = function(){
      var timestamp = +new Date();
      return timestamp+''+(this.id++);
    };

    //instance methods
    MultiEntry.prototype.render_item_template = function() {
      var $template = $(this.item_template.replace(/__name__/g, this.next_id() ));
      $template.addClass('multientry-item new');
      $template.append(this.close_element);
      return $template;
    };

    MultiEntry.prototype.setup_dom = function() {
      this.$el.find('.multientry-item').append(this.close_element);
    };

    MultiEntry.prototype.setup_events = function() {
      var self = this;

      this.$add_button.on('click', function(e){
          e.preventDefault();
          self.add();
      });

      this.$items_container.on('click', '.icon-close', function(e){
          e.preventDefault();
          self.remove($(this).closest('.multientry-item'));
      });
    };

    MultiEntry.prototype.add = function() {
      if(this.opts.limit && this.limit_reached()) {return;}
      var $item = this.render_item_template();
      this.trigger('before:add', {item: $item});
      this.$items_container.append($item);
      this.trigger('add', {item: $item});
      this.opts.limit && this.limit_check();
    };

    MultiEntry.prototype.remove = function($item) {
       if(this.items_count() === 1 && !this.opts.last_item_can_be_removed){return;}
       this.trigger('before:remove', {item: $item});
       $item.remove();
       this.opts.limit && this.limit_check();
       this.trigger('remove', {item: $item});
    };

    MultiEntry.prototype.items_count = function() {
      return this.$items_container.find('.multientry-item').length;
    };

    MultiEntry.prototype.limit_check = function() {
      this.limit_reached() ? this.on_limit_reached() : this.on_limit_valid();
    };

    MultiEntry.prototype.limit_reached = function() {
      return this.items_count() >= this.opts.limit;
    };

    MultiEntry.prototype.on_limit_valid = function() {
      this.opts.show_errors && this.$error.remove();
      this.$add_button.removeClass('disabled');
      this.trigger('limit:valid');
    };

    MultiEntry.prototype.on_limit_reached = function() {
      this.opts.show_errors && this.$items_container.append(this.$error);
      this.$add_button.addClass('disabled');
      this.trigger('limit:reached');
    };


    MultiEntry.prototype.trigger = function(event, data){
      var prefix = 'multientry:';
      data = $.extend({}, data, {instance: this});
      this.$el.trigger(prefix+event, data);
      $(document.body).trigger(prefix+event, data);
    };

      //Expose as jquery plugin
    $.fn.multientry = function(options){
      var method = typeof options === 'string' && options;
      $(this).each(function(){
        var $this = $(this);
        var instance = $this.data('multientry');
        if(instance){
          method && instance[method]();
          return;
        }
        instance = new MultiEntry(this, options);
        $this.data('multientry', instance);
      });
      return this;
    };

    //Expose class
    $.MultiEntry = MultiEntry;

})(jQuery);

