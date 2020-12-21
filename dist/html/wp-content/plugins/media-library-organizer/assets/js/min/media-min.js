var mediaLibraryOrganizerUploader=!1;!function(e,r){void 0!==wp.Uploader&&r.extend(wp.Uploader.prototype,{init:function(){wp.media.events.trigger("mlo:grid:attachment:upload:init")},added:function(e){wp.media.events.trigger("mlo:grid:attachment:upload:added",e)},progress:function(e){wp.media.events.trigger("mlo:grid:attachment:upload:progress",e)},success:function(e){wp.media.events.trigger("mlo:grid:attachment:upload:success",e)},error:function(e){wp.media.events.trigger("mlo:grid:attachment:upload:error",e)},complete:function(){wp.media.events.trigger("mlo:grid:attachment:upload:complete")},refresh:function(){wp.media.events.trigger("mlo:grid:attachment:upload:refresh")}})}(jQuery,_),function(){if(1==media_library_organizer_media.settings.taxonomy_enabled)var e=wp.media.view.AttachmentFilters.extend({id:"media-attachment-taxonomy-filter",createFilters:function(){var e={};_.each(media_library_organizer_media.terms||{},(function(r,t){e[t]={text:r.name+" ("+r.count+")",props:{"mlo-category":r.slug}}})),e.all={text:"All Media Categories",props:{"mlo-category":""},priority:10},e.unassigned={text:"(Unassigned)",props:{"mlo-category":"-1"},priority:10},this.filters=e},change:function(){var e=this.filters[this.el.value];e&&(this.model.set(e.props),wp.media.events.trigger("mlo:grid:filter:change:mlo-category",{slug:e.props["mlo-category"]}))},select:function(){var e=this.model,r="all",t=e.toJSON();wp.media.events.trigger("mlo:grid:filter:select",{props:t}),_.find(this.filters,(function(e,i){var a;if(_.all(e.props,(function(e,r){return e===(_.isUndefined(t[r])?null:t[r])})))return r=i})),this.$el.val(r)}});if(1==media_library_organizer_media.settings.orderby_enabled)var r=wp.media.view.AttachmentFilters.extend({id:"media-attachment-orderby",createFilters:function(){console.log("mlo createFilters");var e={};_.each(media_library_organizer_media.orderby||{},(function(r,t){e[t]={text:r,props:{orderby:t}}})),this.filters=e},select:function(){var e=this.model,r="all",t=e.toJSON();_.find(this.filters,(function(e,i){var a;if(_.all(e.props,(function(e,r){return e===(_.isUndefined(t[r])?null:t[r])})))return r=i})),this.$el.val(r)}});if(1==media_library_organizer_media.settings.order_enabled)var t=wp.media.view.AttachmentFilters.extend({id:"media-attachment-order",createFilters:function(){var e={};_.each(media_library_organizer_media.order||{},(function(r,t){e[t]={text:r,props:{order:t}}})),this.filters=e},select:function(){var e=this.model,r="all",t=e.toJSON();_.find(this.filters,(function(e,i){var a;if(_.all(e.props,(function(e,r){return e===(_.isUndefined(t[r])?null:t[r])})))return r=i})),this.$el.val(r)}});var i=wp.media.view.AttachmentsBrowser;wp.media.view.AttachmentsBrowser=wp.media.view.AttachmentsBrowser.extend({createToolbar:function(){i.prototype.createToolbar.call(this),1==media_library_organizer_media.settings.taxonomy_enabled&&this.toolbar.set("MediaLibraryOrganizerTaxonomyFilter",new e({controller:this.controller,model:this.collection.props,priority:-75}).render()),1==media_library_organizer_media.settings.orderby_enabled&&(console.log(this),this.toolbar.set("MediaLibraryOrganizerTaxonomyOrderBy",new r({controller:this.controller,model:this.collection.props,priority:-75}).render())),1==media_library_organizer_media.settings.order_enabled&&this.toolbar.set("MediaLibraryOrganizerTaxonomyOrder",new t({controller:this.controller,model:this.collection.props,priority:-75}).render())},createAttachmentsHeading:function(){i.prototype.createAttachmentsHeading.call(this),console.log(this),console.log("do something here?")}}),wp.media.query=function(e){return new wp.media.model.Attachments(null,{props:_.extend(_.defaults(e||{},{orderby:media_library_organizer_media.defaults.orderby,order:media_library_organizer_media.defaults.order}),{query:!0})})};var a=wp.media.model.Query,o;_.extend(a,{get:(o=[],function(e,r){var t={},i=a.orderby,n=a.defaultProps,d,l=!1;return delete e.query,delete e.cache,_.defaults(e,n),e.order=e.order.toUpperCase(),"DESC"!==e.order&&"ASC"!==e.order&&(e.order=n.order.toUpperCase()),_.contains(i.allowed,e.orderby)||(e.orderby=n.orderby),_.each(["include","exclude"],(function(r){e[r]&&!_.isArray(e[r])&&(e[r]=[e[r]])})),_.each(e,(function(e,r){_.isNull(e)||(t[a.propmap[r]||r]=e)})),_.defaults(t,a.defaultArgs),t.orderby=i.valuemap[e.orderby]||e.orderby,l=!1,o=[],d||(d=new a([],_.extend(r||{},{props:e,args:t})),o.push(d)),wp.media.events.trigger("mlo:grid:query",{query:d}),d})})}(jQuery,_),wp.media.events.on("mlo:grid:attachment:upload:init",(function(){mediaLibraryOrganizerUploader||void 0===wp.media.frame.uploader||(mediaLibraryOrganizerUploader=wp.media.frame.uploader),mediaLibraryOrganizerUploader&&(mediaLibraryOrganizerUploader.uploader.uploader.settings.multipart_params.media_library_organizer={"mlo-category":media_library_organizer_media.selected_term})})),wp.media.events.on("mlo:grid:filter:change:mlo-category",(function(e){mediaLibraryOrganizerUploader&&(mediaLibraryOrganizerUploader.uploader.uploader.settings.multipart_params.media_library_organizer={"mlo-category":e.slug})})),wp.media.events.on("mlo:grid:attachment:upload:success",(function(e){void 0!==wp.media.frame.library?wp.media.frame.library.props.set({ignore:+new Date}):(wp.media.frame.content.get().collection.props.set({ignore:+new Date}),wp.media.frame.content.get().options.selection.reset())})),jQuery(document).ready((function(e){"undefined"!=typeof mediaLibraryOrganizerSelectizeInit&&mediaLibraryOrganizerSelectizeInit(),e("body").on("click","#mlo-category-tabs a",(function(r){r.preventDefault();var t=e(this).attr("href");e(this).parent().addClass("tabs").siblings("li").removeClass("tabs"),e(".tabs-panel").hide(),e(t).show()}))}));