/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('photo', {
    ID_PHOTO: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      autoIncrement: true
    },
    CHEMIN: {
      type: DataTypes.STRING(70),
      allowNull: false
    },
    ID_ITEM: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'shop',
        key: 'ID_ITEM'
      }
    }
  }, {
    tableName: 'photo'
  });
};
