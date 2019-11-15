/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('illustrer', {
    ID_PHOTO: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      references: {
        model: 'photo',
        key: 'ID_PHOTO'
      }
    },
    ID_EVENTS: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      references: {
        model: 'evenements',
        key: 'ID_EVENTS'
      }
    }
  }, {
    timestamps : false,
    tableName: 'illustrer'
  });
};
