// Imports
var models = require('../models');
var asyncLib = require('async');
var jwtUtils = require('../utils/jwt.utils');

// Constants
const TITLE_LIMIT = 2;
const CONTENT_LIMIT = 4;
const ITEMS_LIMIT = 50;
const ADMIN = 2;

// Routes
module.exports = {
  createEvents: function (req, res) {
    // Getting auth header
    var headerAuth = req.headers['authorization'];
    var ID_MEMBRE = jwtUtils.getUserId(headerAuth);

    // Params
    var EVENTS = req.body.EVENTS;
    var E_DESCRIPTION = req.body.E_DESCRIPTION;
    var ID_REGION = req.body.ID_REGION;
    var E_DATE = new Date();
    var TYPE_UTILISATEUR = req.body.TYPE_UTILISATEUR;
    var ID_PHOTO = req.body.ID_PHOTO;



    if (EVENTS == null || E_DESCRIPTION == null) {
      return res.status(400).json({ 'error': 'missing parameters' });
    }

    if (EVENTS.length <= TITLE_LIMIT || E_DESCRIPTION.length <= CONTENT_LIMIT) {
      return res.status(400).json({ 'error': 'invalid parameters' });
    }

    asyncLib.waterfall([
      function (done) {
        models.membre.findOne({
          where: { ID_MEMBRE: ID_MEMBRE, TYPE_UTILISATEUR: ADMIN || BDE }
        })
          .then(function (userFound) {
            done(null, userFound);
          })
          .catch(function (err) {
            console.log(err);
            return res.status(500).json({ 'error': 'unable to verify user' });
          });
      },
      function (userFound, done) {
        if (userFound) {
          models.evenements.create({
            EVENTS: EVENTS,
            E_DESCRIPTION: E_DESCRIPTION,
            /*ID_MEMBRE : userFound.ID_MEMBRE,*/
            ID_REGION: ID_REGION,
            E_DATE: E_DATE,
            ID_PHOTO: ID_PHOTO
          })
            .then(function (newEvents) {
              done(newEvents);
            });
        } else {
          res.status(404).json({ 'error': 'user not found' });
        }
      },
    ], function (newEvents) {
      if (newEvents) {
        return res.status(201).json(newEvents);
      } else {
        return res.status(500).json({ 'error': 'cannot post message' });
      }
    });

  },
  listEvents: function (req, res) {
    var fields = req.query.fields;
    var limit = parseInt(req.query.limit);
    var offset = parseInt(req.query.offset);
    var order = req.query.order;

    if (limit > ITEMS_LIMIT) {
      limit = ITEMS_LIMIT;
    }

    models.evenements.findAll({
      order: [(order != null) ? order.split(':') : ['EVENTS', 'ASC']],
      attributes: (fields !== '*' && fields != null) ? fields.split(',') : null,
      limit: (!isNaN(limit)) ? limit : null,
      offset: (!isNaN(offset)) ? offset : null,
      /*include: [{
        model: models.membre,
        attributes: [ 'MAIL' ]
      }]*/
    }).then(function (evenements) {
      if (evenements) {
        res.status(200).json(evenements);
      } else {
        res.status(404).json({ "error": "no events found" });
      }
    }).catch(function (err) {
      console.log(err);
      res.status(500).json({ "error": "invalid fields" });
    });
  },
  signEvents: function (req, res) {
    // Getting auth header
    var headerAuth = req.headers['authorization'];
    var ID_MEMBRE = jwtUtils.getUserId(headerAuth);

    // Params
    var ID_EVENTS = req.body.ID_EVENTS;


    if (ID_EVENTS == null) {
      return res.status(400).json({ 'error': 'missing parameters' });
    }

    asyncLib.waterfall([
      function (done) {
        models.membre.findOne({
          where: { ID_MEMBRE: ID_MEMBRE }
        })
          .then(function (userFound) {
            done(null, userFound);
          })
          .catch(function (err) {
            console.log(err);
            return res.status(500).json({ 'error': 'unable to verify user' });
          });
      },
      function (userFound, done) {
        if (userFound) {
          models.inscrire.create({
            ID_EVENTS: ID_EVENTS,
            ID_MEMBRE: ID_MEMBRE
          })
            .then(function (signEvents) {
              done(signEvents);
            }).catch(function (err) {
              console.log(err);
              res.status(500).json({ "error": "already sign" });
            });
        } else {
          res.status(404).json({ 'error': 'user not found' });
        }


      },
    ], function (signEvents) {
      if (signEvents) {
        return res.status(201).json(signEvents);
      } else {
        return res.status(500).json({ 'error': 'cannot post sign to event' });
      }
    });

  }
}