// Imports
var models = require('../models');
var asyncLib = require('async');
var jwtUtils = require('../utils/jwt.utils');

// Constants
const TITLE_LIMIT = 2;
const CONTENT_LIMIT = 4;
const ITEMS_LIMIT = 50;

// Routes
module.exports = {
  createEvents: function (req, res) {
    // Getting auth header
    var headerAuth = req.headers['authorization'];
    var ID_MEMBRE = jwtUtils.getUserId(headerAuth);

    // Params
    var COMMENTAIRE = req.body.COMMENTAIRE;
    var ID_PHOTO = req.body.ID_PHOTO;
    var ID_MEMBRE = req.body.ID_MEMBRE;
    var ID_EVENTS = req.body.ID_EVENTS
    


    if (COMMENTAIRE == null ) {
      return res.status(400).json({ 'error': 'missing parameters' });
    }

    if (COMMENTAIRE.length <= TITLE_LIMIT) {
      return res.status(400).json({ 'error': 'invalid parameters' });
    }

    asyncLib.waterfall([
      function (done) {
        models.inscrire.findOne({
          where: { ID_MEMBRE: ID_MEMBRE, ID_PHOTO: ID_PHOTO }
        })
        models.illustrer.findOne({
            where: {ID_PHOTO: ID_PHOTO, ID_EVENTS: ID_EVENTS}
        })
        models.inscrire.findOne({
            where: {ID_EVENTS: ID_PHOTO, ID_MEMBRE: ID_MEMBRE}
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
            models.commentaire.create({
              COMMENTAIRE: COMMENTAIRE,
              ID_PHOTO: ID_PHOTO
            })
            models.commentaire.create({
              COMMENTAIRE: COMMENTAIRE,
              ID_PHOTO: ID_PHOTO
            })
            models.commentaire.create({
              COMMENTAIRE: COMMENTAIRE,
              ID_PHOTO: ID_PHOTO
            })
            .then(function (newComments) {
              done(newComments);
            });
        } else {
          res.status(404).json({ 'error': 'user not found' });
        }
      },
    ], function (newComments) {
      if (newComments) {
        return res.status(201).json(newComments);
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

    models.commentaire.findAll({
      order: [(order != null) ? order.split(':') : ['EVENTS', 'ASC']],
      attributes: (fields !== '*' && fields != null) ? fields.split(',') : null,
      limit: (!isNaN(limit)) ? limit : null,
      offset: (!isNaN(offset)) ? offset : null,
      /*include: [{
        model: models.membre,
        attributes: [ 'MAIL' ]
      }]*/
    }).then(function (commentaire) {
      if (commentaire) {
        res.status(200).json(commentaire);
      } else {
        res.status(404).json({ "error": "no events found" });
      }
    }).catch(function (err) {
      console.log(err);
      res.status(500).json({ "error": "invalid fields" });
    });
  }
}