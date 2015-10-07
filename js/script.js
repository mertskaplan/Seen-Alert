var app = angular.module('size', []);
app.controller('sizeCtrl', function($scope) {
    $scope.range= "5";
});


$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});