/* Controller where we get the data on da tutor stuffs.*/

(function () {
    'use strict';


    // the tutorcs part comes from the name of the app we created in cs.module.js
    var myApp = angular.module("cs_project", []);

    // datais used in the html file when defining the ng-controller attribute
    myApp.controller("dataControl", function($scope, $http, $window) {

        //Code for search bar
        $scope.query = {};
        $scope.queryBy = "$";
        $scope.menuHighlight = 0;

        // function to send new account information to web api to add it to the database
        $scope.login = function(accountDetails) {
          var accountupload = angular.copy(accountDetails);
          $http.post("login.php", accountupload)
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        var role = response.data.authorizedRole;
                        // successful
                        // send user to proper home page
                        switch (role) {
                            case 'student': $window.location.href = "index_student.html";
                                break;
                            case 'tutor': $window.location.href = "index_tutor.html";
                                break;
                            case 'professor': $window.location.href = "index_faculty.html";
                                break;
                            case 'administrator': $window.location.href = "index_admin.html";
                                break;
                        }
                    }
               } else {
                    alert('unexpected error');
               }
            });
        };


        // function to log the user out
        $scope.logout = function() {
          $http.post("logout.php")
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // send user back to home page
                        $window.location.href = "index.html";
                    }
               } else {
                    alert('unexpected error');
               }
            });
        };

        // function to check if user is logged in
        $scope.checkifloggedin = function() {
          $http.post("isloggedin.php")
            .then(function (response) {
               if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        // successful
                        // set $scope.isloggedin based on whether the user is logged in or not
                        $scope.isloggedin = response.data.loggedin;
                    }
               } else {
                    alert('unexpected error');
               }
            });
        };

        $scope.users = [];
        $scope.getAccounts = function () {
            $http.get('getAccounts.php')
                .then(function (response) {
                    if (response.status == 200) {
                        if (response.data.status == 'error') {
                            alert('error: ' + response.data.message.users);
                        } else {
                            $scope.users = response.data.value.users;
                        }
                    } else {
                        alert('unexpected error');
                    }
                });
        };

        $scope.available_sessions_student = [];
        $scope.getAvailableSessions_student = function () {
            $http.get('getAvailableSessions_student.php')
                .then(function (response) {
                    if (response.status == 200) {
                        if (response.data.status == 'error') {
                            alert('error: ' + response.data.message);
                        } else {
                            $scope.available_sessions_student = response.data.value.sessions;
                            $scope.credits = response.data.value.credits;
                        }
                    } else {
                        alert('unexpected error');
                    }
                });
        };

        $scope.scheduled_sessions_student = [];
        $scope.getScheduledSessions_student = function () {
            $http.get('getScheduledSessions_student.php')
                .then(function (response) {
                    if (response.status == 200) {
                        if (response.data.status == 'error') {
                            alert('error: ' + response.data.message);
                        } else {
                            $scope.scheduled_sessions_student = response.data.value.sessions;
                            $scope.credits = response.data.value.credits;
                        }
                    } else {
                        alert('unexpected error');
                    }
                });
        };

        //function to send new session info to web api to add it to the database
        $scope.newSession = function(sessionDetails) {
            var sessionupload = angular.copy(sessionDetails);
            $http.post("tutor_available.php", sessionupload)
            .then(function(response) {
                if (response.status == 200) {
                if (response.data.status == 'error') {
                    alert('error: ' + response.data.message);
                } else {
                    //successful - send user back to homepage
                    $window.location.href = "index_tutor.html";
                }
                } else {
                alert('unexpected error');
                }
            });
        };

        // Fetch all available sessions for display
        $scope.available_sessions_tutor = [];
        $scope.getAvailableSessions_tutor = function() {
            $http.get('getAvailableSessions_tutor.php')
                .then(function(response) {
                    if (response.status === 200) {
                        if (response.data.status === 'error') {
                            alert('Error: ' + response.data.message);
                        } else {
                            $scope.available_sessions_tutor = response.data.value.sessions;
                        }
                    } else {
                        alert('Something went wrong. Please try again');
                    }
                });
        };

        $scope.scheduled_sessions_tutor = [];
        $scope.getScheduledSessions_tutor = function () {
            $http.get('getScheduledSessions_tutor.php')
                .then(function (response) {
                    if (response.status == 200) {
                        if (response.data.status == 'error') {
                            alert('error: ' + response.data.message);
                        } else {
                            $scope.scheduled_sessions_tutor = response.data.value.sessions;
                        }
                    } else {
                        alert('unexpected error');
                    }
                });
        };

        $scope.studentAddSession = function(session_id) {
          $http.post('studentAddSession.php', {session_id: session_id})
            .then(function(response) {
              if (response.status === 200) {
                if (response.data.status === 'error') {
                  alert('Error: ' + response.data.message);
                } else {
                  $window.location.reload();
                }
              } else {
                alert('Something went wrong. Please try again');
              }
            });
        };

        $scope.setUserEditMode = function(editing, user) {
    			if (editing) {
    			 // Make a copy that we can change without losing the copy from the DB itself
    			 $scope.editUser = angular.copy(user);
    			 user.editMode = true;
    			} else {
    				$scope.editUser = null;
    				user.editMode = false;
    			}
        };

        $scope.updateUser = function(editUser, originalUser) {
    			$http.post('editUser.php', editUser)
    				.then(function(response) {
    					if (response.status === 200) {
    						if (response.data.status === 'error') {
    							alert('Error: ' + response.data.message);
    						} else {
    							$scope.setUserEditMode(false, originalUser);
    							$window.location.reload();
    						}
    					} else {
    						alert('Something went wrong. Please try again');
    					}
    				});
    		};


        $scope.deleteUser = function(firstName, lastName, hawk_id) {
    			if (confirm("Are you sure you want to delete " + firstName + ' ' + lastName + "?")) {
    				$http.post('deleteUser.php', {"hawk_id": hawk_id})
    					.then(function(response) {
    						if (response.status === 200) {
    							if (response.data.status === 'error') {
    								alert('Error: ' + response.data.message);
    							} else {
    								$window.location.reload();
    							}
    						} else {
    							alert('Something went wrong. Please try again');
    						}
    					});
    			}
        };

        $scope.deleteSessionTutor = function(session_id, slot) {
          var cancelTxt = "Are you sure you want to delete session number "
            + session_id + ' on ' + slot + "?";

          if (confirm(cancelTxt)) {
    				$http.post('deleteSessionTutor.php', {"session_id": session_id})
    					.then(function(response) {
    						if (response.status === 200) {
    							if (response.data.status === 'error') {
    								alert('Error: ' + response.data.message);
    							} else {
    								$window.location.reload();
    							}
    						} else {
    							alert('Something went wrong. Please try again');
    						}
    					});
    			}
        };

        $scope.cancelSession_tutor = function(scheduled_id, slot) {
          var deleteTxt = "Are you sure you want to cancel session number " +
            scheduled_id + " on " + slot + "? This will also delete the session.";

          if (confirm(deleteTxt)) {
            $http.post('cancelSession_tutor.php', {"scheduled_id": scheduled_id})
              .then(function(response) {
                if (response.status === 200) {
                  if (response.data.status === 'error') {
                    alert('Error: ' + response.data.message);
                  } else {
                    $window.location.reload();
                  }
                } else {
                  alert('Something went wrong. Please try again');
                }
              });
          }
        };

        $scope.cancelSession_student = function(scheduled_id, slot) {
          var cancelTxt = "Are you sure you want to cancel session number "
            + scheduled_id + ' on ' + slot + "?";

          if (confirm(cancelTxt)) {
            $http.post('cancelSession_student.php', {"scheduled_id": scheduled_id})
              .then(function(response) {
                if (response.status === 200) {
                  if (response.data.status === 'error') {
                    alert('Error: ' + response.data.message);
                  } else {
                    $window.location.reload();
                  }
                } else {
                    alert('Something went wrong. Please try again');
                }
              });
          }
        };

        //create a new account
        $scope.newAccount = function (account) {
            $http.post('admin_add_account.php', account)
              .then(function(response) {
                  if (response.status == 200) {
                      if (response.data.status == 'error') {
                          alert('error: ' + response.data.message);
                      } else {
                          $window.location.reload();
                      }
                  } else {
                      alert('unexpected error');
                  }
              });
        };

        //add new student account
        $scope.newStudentAccount = function(newAccount){
            console.log(newAccount);
            $http.post('newStudentAccount.php', newAccount)
            .then(function(response) {
                if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        $window.location.reload();
                    }
                } else {
                    alert('unexpected error');
                }
            });
        };
                //add new student account
        $scope.newCSVStudentAccount = function(){
            console.log(newAccount);
            $http.post('newCSVstudentAccount.php');
            $window.location.reload();
        };

        //add new course document
        $scope.addDocument = function(doc){
            doc.course_id = $scope.course_id_viewing;
            $http.post('newCourseDoc.php', doc)
            .then(function(response) {
                if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        $window.location.reload();
                    }
                } else {
                    alert('unexpected error');
                }
            });
        };

        $scope.viewingDoc = function(course_id) {
          $scope.course_id_viewing = course_id;
        }

        $scope.facultyCourses = [];
        $scope.getFacultyCourses = function () {
            $http.get('getFacultyCourses.php')
            .then(function(response) {
                if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        $scope.courses = response.data.value.courses;
                    }
                } else {
                        alert('unexpected error');
                    }
                });
        };
        $scope.studentCourses = [];
        $scope.getStudentCourses = function () {
            $http.get('getStudentCourses.php')
            .then(function(response) {
                if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        $scope.courses = response.data.value.courses;
                    }
                } else {
                        alert('unexpected error');
                    }
                });
        };

        $scope.getCourseDocuments = [];
        $scope.getDocuments = function (course_id) {
            $scope.loading = true;
            $http.post('getDocuments.php', {course_id: course_id})
            .then(function(response) {
                $scope.loading = false;
                if (response.status == 200) {
                    if (response.data.status == 'error') {
                        alert('error: ' + response.data.message);
                    } else {
                        $scope.documents = response.data.value.documents;
                    }
                } else {
                        alert('unexpected error');
                    }
                });
        };

        $scope.studentUsers = [];
        $scope.getStudentAccounts = function () {
            $http.get('getStudentAccounts.php')
                .then(function (response) {
                    if (response.status == 200) {
                        if (response.data.status == 'error') {
                            alert('error: ' + response.data.message.users);
                        } else {
                            $scope.users = response.data.value.users;
                        }
                    } else {
                        alert('unexpected error');
                    }
                });
        };

    });

})();
