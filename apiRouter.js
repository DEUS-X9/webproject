//Imports
var express = require('express');
var usersCtrl = require('./routes/usersCtrl');
var eventsCtrl = require('./routes/eventsCtrl');
var shopCtrl = require('./routes/shopCtrl');

// Router
exports.router = (function() {
    var apiRouter = express.Router();

    // Users routes
    apiRouter.route('/users/register/').post(usersCtrl.register);
    apiRouter.route('/users/login/').post(usersCtrl.login);
    apiRouter.route('/users/me/').get(usersCtrl.getUserProfile);
    apiRouter.route('/users/me/').put(usersCtrl.updateUserProfile);

    // Events routes
    apiRouter.route('/events/new/').post(eventsCtrl.createEvents);
    apiRouter.route('/events').get(eventsCtrl.listEvents);

    // Shop routes
    apiRouter.route('/item/new/').post(shopCtrl.createItem);
    apiRouter.route('/item').get(shopCtrl.listItem);

    return apiRouter;
})();