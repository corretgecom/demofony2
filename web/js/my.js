'use strict';

angular.module('discussionShowApp', [
        'discussionShowApp.services',
        'ngCookies',
        'ngResource',
        'ngSanitize',
        'ngRoute',
        'uiGmapgoogle-maps',
        'restangular'

    ]).config(['$interpolateProvider', function($interpolateProvider) {
        $interpolateProvider.startSymbol('[[');
        $interpolateProvider.endSymbol(']]');

    }]).config(function(uiGmapGoogleMapApiProvider) {
        uiGmapGoogleMapApiProvider.configure({
//            key: '', // TODO set Google Maps API key
            v: '3.17',
            language: 'es',
            sensor: false,
            libraries: 'drawing,geometry,visualization'
        });
    })

    .constant('CFG', {
        DELAY: 600,
        RANGE_STEPS: 20,
        GMAPS_ZOOM: 14,
        GPS_CENTER_POS: { lat: 41.4926867, lng: 2.3613954} // Premià de Mar (Barcelona) center
    })
;

'use strict';

angular.module('discussionShowApp').controller('MainCtrl', ['CFG', 'uiGmapGoogleMapApi', '$scope', '$timeout', '$routeParams', '$log', 'Restangular', '$q', 'Security', function(CFG, uiGmapGoogleMapApi, $scope, $timeout, $routeParams, $log, Restangular, $q, Security) {

    $scope.init = function(discussion, comments, isLogged) {
        $scope.discussion = angular.fromJson(discussion);
        $scope.comments = angular.fromJson(comments);
        $scope.is_logged = isLogged;
        $scope.canVotePromise = Security.canVoteInProcessParticipation($scope.discussion.state, $scope.is_logged);
        $scope.map = { zoom: CFG.GMAPS_ZOOM };
        $scope.map.options = {
            scrollwheel: true,
            draggable: true,
            maxZoom: 15
        };
        $scope.map.control = {};
        $log.log($scope.discussion);
        $log.log($scope.comments);
    };

    $scope.vote = function(answer) {
        $scope.canVotePromise.then(function() {
            var url = Routing.generate('api_post_processparticipation_answers_vote', { id: $scope.discussion.id, answer_id: answer.id });
            //substring is to resolve a bug between routing.generate and restangular
            var vote = Restangular.all(url.substring(1));

            if (!answer.user_has_vote_this_proposal_answer) {
                var data = {'comment': null};
                vote.post(data).then(function() {
                    answer.votes_count++;
                    answer.user_has_vote_this_proposal_answer = true;
                    $scope.discussion.user_already_vote = true;
                    $scope.discussion.total_votes_count++;
                });

                return;
            }
            vote.remove().then(function() {
                answer.votes_count--;
                answer.user_has_vote_this_proposal_answer = false;
                $scope.discussion.user_already_vote = false;
                $scope.discussion.total_votes_count--;
            });
        }, function() {
            //failed
        });
    };

    $scope.likeComment = function(comment, index) {
        $scope.canVotePromise.then(function() {
            var url = Routing.generate('api_post_processparticipation_comments_like', { id: $scope.discussion.id, comment_id: comment.id });
            //substring is to resolve a bug between routing.generate and restangular
            var like = Restangular.all(url.substring(1));
            if (!comment.user_already_like) {
                like.post().then(function(result) {
                   $scope.comments.comments[index] = result;
                });

                return;
            }
            like.remove().then(function(result) {
                $scope.comments.comments[index] = result;
            });
        });
    };

    $scope.unlikeComment = function(comment, index) {
        $scope.canVotePromise.then(function() {
            var url = Routing.generate('api_post_processparticipation_comments_unlike', { id: $scope.discussion.id, comment_id: comment.id });
            //substring is to resolve a bug between routing.generate and restangular
            var like = Restangular.all(url.substring(1));
            if (!comment.user_already_unlike) {
                like.post().then(function(result) {
                    $scope.comments.comments[index] = result;
                });

                return;
            }
            like.remove().then(function(result) {
                $scope.comments.comments[index] = result;
            });
        });
    };

    uiGmapGoogleMapApi.then(function (maps) {
        // promise done
        $log.log('uiGmapGoogleMapApi loaded', maps);
    });

}]);

'use strict';

var services = angular.module('discussionShowApp.services', []);

services.factory('Security', function($q) {
    return {
        canVoteInProcessParticipation: function(state, is_logged) {
              return $q(function(resolve, reject) {
                if (state === 2 && is_logged) {
                    resolve();
                } else {
                    reject('Vote is not open');
                }
            });
        }
    };
});