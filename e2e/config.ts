interface Config {
  baseURL: string;
  userName: string;
  userPassword: string;
}

const config: Config = {
  baseURL: process.env.BASE_URL || '',
  userName: process.env.E2E_USER_NAME || '',
  userPassword: process.env.E2E_USER_PASSWORD || '',
};

export default config;