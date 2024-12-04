import app from 'flarum/admin/app';
import Page from './Page';

app.initializers.add('xypp/flarum-limited-request', () => {
  app.extensionData.for('xypp-limited-request').registerPage(Page);
});
