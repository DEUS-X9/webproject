// Imports
var bcrypt = require('bcrypt');
var jwtUtils = require('../utils/jwt.utils');
var models = require('../models');
var asyncLib = require('async');

//Constantes
const EMAIL_REGEX = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
const PASSWORD_REGEX = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,}$/;
const ADMIN = 4;
const BDE = 2;

//Routes
module.exports = {
  register: function (req, res) {
    // Params
    var MAIL = req.body.MAIL;
    var NOM = req.body.NOM;
    var PRENOM = req.body.PRENOM;
    var PASSWORD = req.body.PASSWORD;
    var ID_REGION = req.body.ID_REGION;

    if (MAIL == null || NOM == null || PRENOM == null || PASSWORD == null) {
      return res.status(400).json({ 'error': 'missing parameters' });
    }

    if (!EMAIL_REGEX.test(MAIL)) {
      return res.status(400).json({ 'error': 'email is not valid' });

    }

    if (!PASSWORD_REGEX.test(PASSWORD)) {
      return res.status(400).json({ 'error': 'password invalid' });

    }

    asyncLib.waterfall([
      function (done) {
        models.membre.findOne({
          attributes: ['MAIL'],
          where: { MAIL: MAIL }
        })
          .then(function (userFound) {
            done(null, userFound);
          })
          .catch(function (err) {
            return res.status(500).json({ 'error': 'unable to verify user' });
          });
      },
      function (userFound, done) {
        if (!userFound) {
          bcrypt.hash(PASSWORD,5 , function (err, bcryptedPassword) {
            done(null, userFound, bcryptedPassword);
          });
        } else {
          return res.status(409).json({ 'error': 'user already exist' });
        }
      },

      function (userFound, bcryptedPassword, done) {
        var newUser = models.membre.create({
          MAIL: MAIL,
          NOM: NOM,
          PRENOM: PRENOM,
          PASSWORD: bcryptedPassword,
          TYPE_UTILISATEUR: 1,
          actif: 1,
          ID_REGION: ID_REGION
        })
          .then(function (newUser) {
            done(newUser);
          })
          .catch(function (err) {
            //console.log(err);
            return res.status(500).json({ 'error': 'cannot add user' });
          });
      }
    ], function (newUser) {
      if (newUser) {
        return res.status(201).json({
          'ID_MEMBRE': ID_MEMBRE
        });
      } else {
        return res.status(500).json({ 'error': 'cannot add user' });
      }
    });
  },
  login: function (req, res) {

    // Params
    var MAIL = req.body.MAIL;
    var PASSWORD = req.body.PASSWORD;
    var NOM = req.body.NOM;
    var PRENOM = req.body.PRENOM;

    if (MAIL == null || PASSWORD == null) {
      return res.status(400).json({ 'error': 'missing parameters' })
    }

    asyncLib.waterfall([
      function (done) {
        models.membre.findOne({
          where: { MAIL: MAIL }
        })
          .then(function (userFound) {
            done(null, userFound);
          })
          .catch(function (err) {
            return res.status(500).json({ 'error': 'unable to verify user' });
          });
      },
      function (userFound, done) {
        if (userFound) {
          bcrypt.compare(PASSWORD, userFound.PASSWORD, function (errBycrypt, resBycrypt) {
            done(null, userFound, resBycrypt);
          });
        } else {
          return res.status(404).json({ 'error': 'user not exist in DB' });
        }
      },
      function (userFound, resBycrypt, done) {
        if (resBycrypt) {
          done(userFound);
        } else {
          return res.status(403).json({ 'error': 'invalid password' });
        }
      }
    ], function (userFound) {
      if (userFound) {
        return res.status(201).json({
          'ID_MEMBRE': userFound.ID_MEMBRE,
          'NOM': userFound.NOM,
          'PRENOM': userFound.PRENOM,
          'MAIL': MAIL,
          'token': jwtUtils.generateTokenForUser(userFound)
        });
      } else {
        return res.status(500).json({ 'error': 'cannot log on user' });
      }
    });
  },
  getUserProfile: function (req, res) {
    // Getting auth header
    var headerAuth = req.headers['authorization'];
    var ID_MEMBRE = jwtUtils.getUserId(headerAuth);

    if (ID_MEMBRE < 0)
      return res.status(400).json({ 'error': 'wrong token' });

    models.membre.findOne({
      attributes: ['ID_MEMBRE', 'MAIL', 'NOM', 'PRENOM'],
      where: { ID_MEMBRE: ID_MEMBRE }
    }).then(function (membre) {
      if (membre) {
        res.status(201).json(membre);
      } else {
        res.status(404).json({ 'error': 'user not found' });
      }
    }).catch(function (err) {
      res.status(500).json({ 'error': 'cannot fetch user' });
    });
  },
  updateUserProfile: function (req, res) {
    // Getting auth header
    var headerAuth = req.headers['authorization'];
    var ID_MEMBRE = jwtUtils.getUserId(headerAuth);

    // Params
    var PRENOM = req.body.PRENOM;
    var NOM = req.body.NOM;

    asyncLib.waterfall([
      function (done) {
        models.membre.findOne({
          attributes: ['ID_MEMBRE', 'NOM', 'PRENOM'],
          where: { ID_MEMBRE: ID_MEMBRE }
        }).then(function (userFound) {
          done(null, userFound);
        })
          .catch(function (err) {
            return res.status(500).json({ 'error': 'unable to verify user' });
          });
      },
      function (userFound, done) {
        if (userFound) {
          userFound.update({
            PRENOM: (PRENOM ? PRENOM : userFound.PRENOM),
            NOM: (NOM ? NOM : userFound.NOM)
          }).then(function () {
            done(userFound);
          }).catch(function (err) {
            res.status(500).json({ 'error': 'cannot update user' });
          });
        } else {
          res.status(404).json({ 'error': 'user not found' });
        }
      },
    ], function (userFound) {
      if (userFound) {
        return res.status(201).json(userFound);
      } else {
        return res.status(500).json({ 'error': 'cannot update user profile' });
      }
    });
  },
  listUser: function (req, res) {
    var headerAuth = req.headers['authorization'];
    var ID_MEMBRE = jwtUtils.getUserId(headerAuth);

    var fields = req.query.fields;
    var limit = parseInt(req.query.limit);
    var offset = parseInt(req.query.offset);
    var order = req.query.order;
    var TYPE_UTILISATEUR = req.body.TYPE_UTILISATEUR;

    /*if (limit > ITEMS_LIMIT) {
      limit = ITEMS_LIMIT;
    }*/

    asyncLib.waterfall([
        function (done) {
          models.membre.findOne({
              where: { ID_MEMBRE: ID_MEMBRE, TYPE_UTILISATEUR: BDE || ADMIN }
            })
              .then(function (userFound) {
                done(null, userFound);
              })
              .catch(function (err) {
                console.log(err);
                return res.status(500).json({ 'error': 'unable to verify user or do not have the right' });
              });
          },
        function (userFound, done) {
          if (userFound) {
            models.membre.findAll({
              attributes: ['ID_MEMBRE', 'MAIL', 'NOM', 'PRENOM', 'ID_REGION', 'TYPE_UTILISATEUR'],
              }).then(function (membre) {
                if (membre) {
                  res.status(200).json(membre);
                } else {
                  res.status(404).json({ "error": "no item found" });
                }
              }).catch(function (err) {
                console.log(err);
                res.status(500).json({ "error": "invalid fields" });
              });
          } else {
              console.log(err);
            res.status(404).json({ 'error': 'user not found' });
          }
        },
      ], function (newCart) {
        if (newCart) {
          return res.status(201).json(newCart);
        } else {
          return res.status(500).json({ 'error': 'cannot put in cart' });
        }
      });
    
  }
}
