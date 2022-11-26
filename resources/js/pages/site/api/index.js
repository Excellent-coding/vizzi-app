import axios from "axios";
import Cookies from "js-cookie";

const instance = axios.create({
  baseURL: "/api"
});

instance.interceptors.request.use(config => {
  const token = Cookies.get("token");
  if (token) {
    config.headers.authorization = `Bearer ${token}`;
  }
  return config;
});

export default {
  getAllPolls() {
    return instance.get("/poll");
  },
  getPoll(poll_id) {
    return instance.get(`/poll/${poll_id}`);
  },
  updatePoll(poll_id, data) {
    return instance.put(`/poll/${poll_id}`, data);
  },
  createPoll(data) {
    return instance.post("poll", data);
  },
  deletePoll(poll_id) {
    return instance.delete(`/poll/${poll_id}`);
  },
  getAvailablePolls() {
    return instance.get("/poll/available");
  },
  answersToAnswers(poll_id, answer) {
    return instance.post(`/poll/${poll_id}/answer`, { answer });
  },
  getStatByPollIds(ids) {
    return instance.post("/poll/stat", { ids });
  }
};
