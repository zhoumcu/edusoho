webpackJsonp(["app/js/course-manage/index"],{d14d05cad9e7abf02a5d:function(e,s){"use strict";Object.defineProperty(s,"__esModule",{value:!0});var t=s.toggleIcon=function(e,s,t){var a=e.find(".js-remove-icon"),n=e.find(".js-remove-text");a.hasClass(s)?(a.removeClass(s).addClass(t),n?n.text(Translator.trans("收起")):""):(a.removeClass(t).addClass(s),n?n.text(Translator.trans("展开")):"")};s.chapterAnimate=function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"body",s=arguments.length>1&&void 0!==arguments[1]?arguments[1]:".js-task-chapter",a=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"es-icon-remove",n=arguments.length>3&&void 0!==arguments[3]?arguments[3]:"es-icon-anonymous-iconfont";$(e).on("click",s,function(e){var i=$(e.currentTarget);i.nextUntil(s).animate({height:"toggle",opacity:"toggle"},"normal"),t(i,a,n)})}},"4e68e437f5b716377a9d":function(e,s,t){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(s,"__esModule",{value:!0}),s.TaskListHeaderFixed=s.updateTaskNum=s.TabChange=s.showSettings=s.unpublishTask=s.publishTask=s.deleteTask=s.publishCourse=s.deleteCourse=s.closeCourse=s.taskSortable=s.sortablelist=void 0;var n=t("b334fd7e4c5a19234db2"),i=a(n),r=t("8f840897d9471c8c1fbd"),o=a(r),l=s.sortablelist=function(e){var s=$(e),t=s.sortable("serialize").get(),a=0,n=0,i=0;s.find(".task-manage-item").each(function(){var e=$(this);e.hasClass("js-task-manage-item")?e.find(".number").length>0&&(a++,e.find(".number").text(a)):e.hasClass("task-manage-unit")?(i++,e.find(".number").text(i)):e.hasClass("task-manage-chapter")&&(n++,i=0,e.find(".number").text(n))}),s.trigger("finished"),$.post(s.data("sortUrl"),{ids:t},function(e){})};s.taskSortable=function(e){$(e).length&&(0,o.default)({element:e,ajax:!1},function(s){l(e)})},s.closeCourse=function(){$("body").on("click",".js-close-course",function(e){var s=$(e.currentTarget);confirm(Translator.trans("course.manage.close_hint"))&&$.post(s.data("check-url"),function(e){e.warn&&!confirm(Translator.trans(e.message))||$.post(s.data("url"),function(e){e.success?((0,i.default)("success",Translator.trans("course.manage.close_success_hint")),location.reload()):(0,i.default)("danger",Translator.trans("course.manage.close_fail_hint")+":"+e.message)})})})},s.deleteCourse=function(){$("body").on("click",".js-delete-course",function(e){confirm(Translator.trans("course.manage.delete_hint"))&&$.post($(e.currentTarget).data("url"),function(e){e.success?((0,i.default)("success",Translator.trans("site.delete_success_hint")),e.redirect?window.location.href=e.redirect:location.reload()):(0,i.default)("danger",Translator.trans("site.delete_fail_hint")+":"+e.message)})})},s.publishCourse=function(){$("body").on("click",".js-publish-course",function(e){confirm(Translator.trans("course.manage.publish_hint"))&&$.post($(e.target).data("url"),function(e){e.success?((0,i.default)("success",Translator.trans("course.manage.task_publish_success_hint")),location.reload()):(0,i.default)("danger",Translator.trans("course.manage.task_publish_fail_hint")+":"+e.message,{delay:5e3})})})},s.deleteTask=function(){$("body").on("click",".delete-item",function(e){if("task"==$(e.currentTarget).data("type")){if(!confirm(Translator.trans("course.manage.task_delete_hint")))return}else if("chapter"==$(e.currentTarget).data("type")&&!confirm(Translator.trans("course.manage.chapter_delete_hint")))return;$.post($(e.currentTarget).data("url"),function(s){s.success?((0,i.default)("success",Translator.trans("site.delete_success_hint")),$(e.target).parents(".task-manage-item").remove(),l("#sortable-list"),$("#sortable-list").children("li").length<1&&$(".js-task-empty").hasClass("hidden")&&$(".js-task-empty").removeClass("hidden"),document.location.reload()):(0,i.default)("danger",Translator.trans("site.delete_fail_hint")+":"+s.message)})})},s.publishTask=function(){$("body").on("click",".publish-item",function(e){$.post($(e.target).data("url"),function(s){if(s.success){var t=$(e.target).closest(".task-manage-item");(0,i.default)("success",Translator.trans("course.manage.task_publish_success_hint")),$(t).find(".publish-item").addClass("hidden"),$(t).find(".delete-item").addClass("hidden"),$(t).find(".unpublish-item").removeClass("hidden"),$(t).find(".publish-status").addClass("hidden")}else(0,i.default)("danger",Translator.trans("course.manage.task_publish_fail_hint")+":"+s.message)})})},s.unpublishTask=function(){$("body").on("click",".unpublish-item",function(e){$.post($(e.target).data("url"),function(s){if(s.success){var t=$(e.target).closest(".task-manage-item");(0,i.default)("success",Translator.trans("course.manage.task_unpublish_success_hint")),$(t).find(".publish-item").removeClass("hidden"),$(t).find(".delete-item").removeClass("hidden"),$(t).find(".unpublish-item").addClass("hidden"),$(t).find(".publish-status").removeClass("hidden")}else(0,i.default)("danger",Translator.trans("course.manage.task_unpublish_fail_hint")+":"+s.message)})})},s.showSettings=function(){$("#sortable-list").on("click",".js-item-content",function(e){var s=$(e.currentTarget),t=s.closest(".js-task-manage-item");t.hasClass("active")?t.removeClass("active").find(".js-settings-list").stop().slideUp(500):(t.addClass("active").find(".js-settings-list").stop().slideDown(500),t.siblings(".js-task-manage-item.active").removeClass("active").find(".js-settings-list").hide())})},s.TabChange=function(){$('[data-role="tab"]').click(function(e){var s=$(this);$(s.data("tab-content")).removeClass("hidden").siblings('[data-role="tab-content"]').addClass("hidden")})},s.updateTaskNum=function(e){},s.TaskListHeaderFixed=function(){var e=$(".js-task-list-header");if(e.length){var s=e.offset().top;$(window).scroll(function(t){$(window).scrollTop()>=s?e.addClass("fixed"):e.removeClass("fixed")})}}},0:function(e,s,t){"use strict";var a=t("4e68e437f5b716377a9d"),n=t("d14d05cad9e7abf02a5d");$('[data-help="popover"]').popover();var i="#sortable-list";(0,a.taskSortable)(i),(0,a.updateTaskNum)(i),(0,a.closeCourse)(),(0,a.deleteCourse)(),(0,a.deleteTask)(),(0,a.publishTask)(),(0,a.unpublishTask)(),(0,a.showSettings)(),(0,a.TaskListHeaderFixed)(),$("#sortable-list").on("click",".js-chapter-toggle-show",function(e){var s=$(e.currentTarget),t=s.closest(".js-task-manage-chapter");t.nextUntil(".js-task-manage-chapter").animate({height:"toggle",opacity:"toggle"},"normal"),(0,n.toggleIcon)(t,"es-icon-keyboardarrowdown","es-icon-keyboardarrowup")})}});