/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('inscrire', {
    ID_EVENTS: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      references: {
        model: 'evenements',
        key: 'ID_EVENTS'
      }
    },
    ID_MEMBRE: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true,
      references: {
        model: 'membre',
        key: 'ID_MEMBRE'
      }
    }
  }, {
    tableName: 'inscrire'
  });
};
