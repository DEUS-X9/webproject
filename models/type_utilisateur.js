/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('type_utilisateur', {
    TYPE_UTILISATEUR: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    NOM_TYPE: {
      type: DataTypes.STRING(50),
      allowNull: false
    }
  }, {
    timestamps : false,
    tableName: 'type_utilisateur'
  });
};
