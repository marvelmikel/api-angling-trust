export default async (api, root) => {
    const getCategories = async (catId)  => api.get(
        `membership_type_categories?membership_type_id=${catId}&at_member=1&fl_member=1`
    );

    const maybeGetCategories = () => new Promise(async (resolve, reject) => {
        if (root.member) {
            const { data } = await getCategories(root.member.membership_type_id);

            return resolve(data.data.categories.map(category => ({
                id: category.slug,
                name: category.name,
            })));
        }

        setTimeout(() => resolve(maybeGetCategories()), 100);
    });

    return maybeGetCategories()

}
