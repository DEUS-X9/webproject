/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('commentaire', {
    ID_COMMENTAIRE: {
      type: DataTypes.STRING(70),
      allowNull: false,
      primaryKey: true
    },
    COMMENTAIRE: {
      type: DataTypes.STRING(70),
      allowNull: false
    },
    ID_PHOTO: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      references: {
        model: 'photo',
        key: 'ID_PHOTO'
      }
    },
    ID_MEMBRE: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'membre',
        key: 'ID_MEMBRE'
      }
    }
  }, {
    tableName: 'commentaire'
  });
};
