/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('categorie', {
    ID_CATEGORIE: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    NOM_CATEGORIE: {
      type: DataTypes.STRING(50),
      allowNull: false
    }
  }, {
    timestamps : false,
    tableName: 'categorie'
  });
};
