angular.module('App', ['angular-loading-bar'])

.service('dataAPI', function ($q, $http) {
    
return {
    get: function (action, page) {
        var deferred = $q.defer(),
            page = page || 0;
        $http.get('http://oneway.vn/api/api-v3/updater/data.php?action=' + action + '&page=' + page).success(function (data) {
            deferred.resolve(data);
        }).error(function () {
            deferred.reject();
        });

        return deferred.promise;

    },
    
    count: function () {
        var deferred = $q.defer();
        $http.get('http://oneway.vn/api/api-v3/updater/data.php?action=count').success(function (data) {
            deferred.resolve(data);
        }).error(function () {
            deferred.reject();
        });

        return deferred.promise;
    },
    
    update: function (id, value) {
        var deferred = $q.defer();
        $http.post('http://oneway.vn/api/api-v3/index.php/audio/update-meta',
            $.param( {id: id, key: 'duration', value: value} ),
            { headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8' } }
        ).success(function (data) {
            deferred.resolve(data);
        }).error(function () {
            deferred.reject();
        });

        return deferred.promise;
    },
    
    remove: function (id) {
        var deferred = $q.defer();
        $http.post('http://oneway.vn/api/api-v3/index.php/audio/update-meta',
            $.param( {id: id, key: 'status', value: 'private'} ),
            { headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8' } }
        ).success(function (data) {
            deferred.resolve(data);
        }).error(function () {
            deferred.reject();
        });

        return deferred.promise;
    }
    
};
    
})

.controller('App', function ($scope, dataAPI) {
    
    // Get number of pages
    dataAPI.count().then(function (data) {
        $scope.pages = [];
        for(var i = 0; i < Math.ceil(data.count / data.limit); i++) {
            $scope.pages.push(i + 1);
        }
        //console.log($scope.pages);
    });
    
    
    
    
    
    // Main data
    $scope.data = [];
    $scope.refreshed = false;
    
    
    
    // Actions
    $scope.actions = {
        
        
        
        // Get all audio (by page)
        all: function (page) {
            dataAPI.get('all', page).then(function (data) {
                $scope.data = data;
                $scope.refreshed = false;
                //console.log(data);
            }, function () {
                alert('Loading data fails!');
            });
        },
        
        
        
        // Get audio without duration
        check: function () {
            dataAPI.get('check').then(function (data) {
                $scope.data = data;
                $scope.refreshed = false;
                //console.log(data);
            }, function () {
                alert('Loading data fails!');
            });
        },
        
        
        refreshAll: function () {
            if($scope.data.length > 0) {   
                
                for(var i = 0; i < $scope.data.length; i++) {
                    this.refreshThis($scope.data[i].id);
                }
                
                $scope.refreshed = true;
                
            } else {
                alert('Oops! Load data first!');   
            }
        },
        
        refreshThis: function (id) {
            var src = '';
            for(var i = 0; i < $scope.data.length; i++) {
                if($scope.data[i].id === id) {
                    src = $scope.data[i].src;
                }                
            }
            
            var audio = new Audio(src);
            audio.addEventListener('canplay', function (e) {
                if( ! isNaN(this.duration) && this.duration > 0 ) {
                    //console.log('Load meta done: ' + id, this.duration);
                    $scope.actions.updateLocal(id, this.duration);
                }
                this.src = null;
            });
        },
        
        updateLocal: function (id, value) {
            for(var i = 0; i < $scope.data.length; i++) {
                if($scope.data[i].id === id) {
                    $scope.data[i].freshDuration = value;                    
                    $scope.$digest();
                    //console.log(value, Math.round(value));
                }                
            }
        },
        
        updateRemote: function (id, value) {
            dataAPI.update(id, value).then(function (data) {
                if(data) {
                    alert('OK!');
                } else {
                    alert('Update fails!');
                }
            }, function () {
                alert('Update fails!');
            });
        },
        
        
        updateThis: function (id) {
            var duration = null,
                index = null;
            
            for(var i = 0; i < $scope.data.length; i++) {
                if($scope.data[i].id === id) {
                    index = i;
                }                
            }
            
            if($scope.data[index].freshDuration && $scope.data[index].freshDuration > 0) {
                duration = $scope.data[index].freshDuration;
            }
            
            if(index == null || duration === null) {
                alert('Refresh first!');
                return false;
            }
            
            dataAPI.update(id, duration).then(function (data) {
                if(data) {
                    $scope.data[index].ok = true;
                } else {
                    $scope.data[index].ok = false;
                }
            },function () {
                $scope.data[index].ok = false;
            });
            
        },
        
        updateAll: function () {
            for(var i = 0; i < $scope.data.length; i++) {
                if(!$scope.data[i].freshDuration || $scope.data[i].freshDuration <= 0) {
                    continue;
                }
                this.updateThis($scope.data[i].id);
            }
        },
        
        
        
        removeThis:function (id) {
            
            var index = null;
            
            for(var i = 0; i < $scope.data.length; i++) {
                if($scope.data[i].id === id) {
                    index = i;
                }                
            }
            
            if(index == null) {
                return false;
            }
            
            if($scope.data[index].freshDuration && $scope.data[index].freshDuration > 0) {
                return false;
            }
            
            
            dataAPI.remove(id).then(function (data) {
                if(data) {
                    $scope.data.splice(index, 1);                    
                    if(!$scope.$$phase) {
                        $scope.$digest();
                    }
                }
            }, function () {
                return false;
            });
        }
        
//        removeAll: function () {
//            for(var i = 0; i < $scope.data.length; i++) {
//                this.removeThis($scope.data[i].id);
//            }
//        }
        
        
        
    };
    
    
    
//    function readableDuration(seconds) {
//        sec = Math.floor( seconds );    
//        min = Math.floor( sec / 60 );
//        min = min >= 10 ? min : '0' + min;    
//        sec = Math.floor( sec % 60 );
//        sec = sec >= 10 ? sec : '0' + sec;    
//        return min + ':' + sec;
//    }
    
    
    
});