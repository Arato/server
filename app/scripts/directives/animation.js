'use strict';

angular.module('aratoappApp')
    .directive('animation', animation)

animation.$inject = [];
function animation() {
    var directive = {
        template: '<div></div>',
        restrict: 'E',
        link    : postLink
    };
    return directive;

    function postLink(scope, element, attrs) {
        element.text('this is the animation directive');
    }
}